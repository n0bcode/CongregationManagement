#!/bin/bash

# Script để xem preview các thay đổi trước khi backup
# Sử dụng: ./preview-changes.sh <commit-id>

set -e

# Màu sắc
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m'

if [ -z "$1" ]; then
    echo -e "${RED}Error: Vui lòng cung cấp commit ID${NC}"
    echo "Sử dụng: $0 <commit-id>"
    echo ""
    echo "Ví dụ:"
    echo "  $0 abc123def          # Từ commit cụ thể"
    echo "  $0 HEAD~10            # Từ 10 commit trước"
    echo "  $0 v1.0.0             # Từ tag"
    exit 1
fi

COMMIT_ID=$1
PROJECT_DIR="managing-congregation"

# Kiểm tra commit
if ! git rev-parse --verify "$COMMIT_ID" >/dev/null 2>&1; then
    echo -e "${RED}Error: Commit ID '$COMMIT_ID' không tồn tại${NC}"
    exit 1
fi

echo -e "${BLUE}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║         Preview Thay Đổi - Managing Congregation          ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""

# Thông tin commit
echo -e "${CYAN}📋 Thông tin Commit:${NC}"
echo -e "${YELLOW}   Từ commit:${NC} $COMMIT_ID ($(git log -1 --format=%s $COMMIT_ID))"
echo -e "${YELLOW}   Đến commit:${NC} $(git rev-parse --short HEAD) ($(git log -1 --format=%s HEAD))"
echo -e "${YELLOW}   Thời gian:${NC} $(git log -1 --format=%ar $COMMIT_ID) → $(git log -1 --format=%ar HEAD)"
echo ""

# Đếm số commit
COMMIT_COUNT=$(git rev-list --count "$COMMIT_ID"..HEAD -- "$PROJECT_DIR")
echo -e "${CYAN}📊 Số commit liên quan:${NC} ${GREEN}${COMMIT_COUNT}${NC}"
echo ""

# Danh sách file thay đổi
echo -e "${CYAN}📁 Danh sách file thay đổi:${NC}"
CHANGED_FILES=$(git diff --name-status "$COMMIT_ID" HEAD -- "$PROJECT_DIR")

if [ -z "$CHANGED_FILES" ]; then
    echo -e "${YELLOW}   Không có file nào thay đổi${NC}"
    exit 0
fi

# Phân loại file theo trạng thái
ADDED=$(echo "$CHANGED_FILES" | grep "^A" | wc -l)
MODIFIED=$(echo "$CHANGED_FILES" | grep "^M" | wc -l)
DELETED=$(echo "$CHANGED_FILES" | grep "^D" | wc -l)
RENAMED=$(echo "$CHANGED_FILES" | grep "^R" | wc -l)

echo -e "   ${GREEN}✓ Thêm mới:${NC} $ADDED file"
echo -e "   ${BLUE}✎ Sửa đổi:${NC} $MODIFIED file"
echo -e "   ${RED}✗ Xóa:${NC} $DELETED file"
echo -e "   ${YELLOW}↻ Đổi tên:${NC} $RENAMED file"
echo ""

# Hiển thị chi tiết từng file
echo -e "${CYAN}📝 Chi tiết từng file:${NC}"
echo "$CHANGED_FILES" | while IFS=$'\t' read -r status file rest; do
    case $status in
        A)
            echo -e "   ${GREEN}[+]${NC} $file"
            ;;
        M)
            echo -e "   ${BLUE}[M]${NC} $file"
            ;;
        D)
            echo -e "   ${RED}[-]${NC} $file"
            ;;
        R*)
            echo -e "   ${YELLOW}[R]${NC} $file → $rest"
            ;;
        *)
            echo -e "   [?] $file"
            ;;
    esac
done
echo ""

# Thống kê thay đổi
echo -e "${CYAN}📈 Thống kê thay đổi:${NC}"
git diff --stat "$COMMIT_ID" HEAD -- "$PROJECT_DIR" | while read line; do
    echo "   $line"
done
echo ""

# Phân loại theo loại file
echo -e "${CYAN}🗂️  Phân loại theo loại file:${NC}"
echo "$CHANGED_FILES" | awk '{print $2}' | sed 's/.*\.//' | sort | uniq -c | sort -rn | while read count ext; do
    echo -e "   ${YELLOW}.$ext${NC}: $count file"
done
echo ""

# Phân loại theo thư mục
echo -e "${CYAN}📂 Phân loại theo thư mục:${NC}"
echo "$CHANGED_FILES" | awk '{print $2}' | sed 's|/[^/]*$||' | sort | uniq -c | sort -rn | head -10 | while read count dir; do
    echo -e "   ${YELLOW}$dir${NC}: $count file"
done
echo ""

# Top contributors
echo -e "${CYAN}👥 Top Contributors:${NC}"
git shortlog -sn "$COMMIT_ID"..HEAD -- "$PROJECT_DIR" | head -5 | while read count name; do
    echo -e "   ${GREEN}$name${NC}: $count commits"
done
echo ""

# Ước tính kích thước backup
echo -e "${CYAN}💾 Ước tính kích thước backup:${NC}"
TOTAL_SIZE=0
echo "$CHANGED_FILES" | awk '{print $2}' | while read file; do
    if [ -f "$file" ]; then
        SIZE=$(stat -f%z "$file" 2>/dev/null || stat -c%s "$file" 2>/dev/null || echo 0)
        TOTAL_SIZE=$((TOTAL_SIZE + SIZE))
    fi
done

# Tính tổng size (cách khác vì biến trong while loop không persist)
TOTAL_SIZE=$(git diff --name-only "$COMMIT_ID" HEAD -- "$PROJECT_DIR" | while read file; do
    if [ -f "$file" ]; then
        stat -f%z "$file" 2>/dev/null || stat -c%s "$file" 2>/dev/null || echo 0
    fi
done | awk '{sum+=$1} END {print sum}')

if [ -n "$TOTAL_SIZE" ] && [ "$TOTAL_SIZE" -gt 0 ]; then
    SIZE_MB=$(echo "scale=2; $TOTAL_SIZE / 1024 / 1024" | bc)
    SIZE_KB=$(echo "scale=2; $TOTAL_SIZE / 1024" | bc)
    
    if [ $(echo "$SIZE_MB > 1" | bc) -eq 1 ]; then
        echo -e "   Kích thước: ~${YELLOW}${SIZE_MB} MB${NC}"
    else
        echo -e "   Kích thước: ~${YELLOW}${SIZE_KB} KB${NC}"
    fi
    
    # Ước tính sau khi nén (thường 20-30% kích thước gốc)
    COMPRESSED_MB=$(echo "scale=2; $SIZE_MB * 0.25" | bc)
    echo -e "   Sau nén: ~${YELLOW}${COMPRESSED_MB} MB${NC} (ước tính)"
else
    echo -e "   ${YELLOW}Không thể tính toán kích thước${NC}"
fi
echo ""

# Gợi ý lệnh backup
echo -e "${BLUE}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║                    Lệnh để backup                          ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "${GREEN}Backup đầy đủ:${NC}"
echo -e "   ${CYAN}./backup-changes.sh $COMMIT_ID${NC}"
echo ""
echo -e "${GREEN}Backup đơn giản:${NC}"
echo -e "   ${CYAN}./backup-simple.sh $COMMIT_ID${NC}"
echo ""
echo -e "${GREEN}Xem diff chi tiết:${NC}"
echo -e "   ${CYAN}git diff $COMMIT_ID HEAD -- $PROJECT_DIR${NC}"
echo ""
