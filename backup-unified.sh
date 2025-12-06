#!/bin/bash

# Script thống nhất để preview và backup các thay đổi trong managing-congregation
# Kết hợp tính năng của backup-changes.sh, backup-simple.sh và preview-changes.sh
#
# Sử dụng:
#   ./backup-unified.sh preview <commit-id>     # Preview thay đổi
#   ./backup-unified.sh simple <commit-id>      # Backup đơn giản
#   ./backup-unified.sh full <commit-id>        # Backup đầy đủ
#   ./backup-unified.sh <commit-id>             # Tự động chọn mode dựa trên kích thước thay đổi

set -e  # Dừng script nếu có lỗi

# Màu sắc cho output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
MAGENTA='\033[0;35m'
NC='\033[0m' # No Color

# Cấu hình mặc định
PROJECT_DIR="managing-congregation"
AUTO_THRESHOLD=50  # Số file tối đa để tự động chọn simple mode

# Hàm hiển thị help
show_help() {
    echo -e "${BLUE}╔══════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${BLUE}║              Backup Unified Script - Help                    ║${NC}"
    echo -e "${BLUE}╚══════════════════════════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "${CYAN}Sử dụng:${NC}"
    echo -e "  ${GREEN}$0 preview <commit-id>${NC}     # Preview thay đổi trước khi backup"
    echo -e "  ${GREEN}$0 simple <commit-id>${NC}      # Backup đơn giản (nhanh, ít file)"
    echo -e "  ${GREEN}$0 full <commit-id>${NC}        # Backup đầy đủ (chi tiết, nhiều file)"
    echo -e "  ${GREEN}$0 <commit-id>${NC}             # Tự động chọn mode dựa trên số file thay đổi"
    echo ""
    echo -e "${CYAN}Ví dụ:${NC}"
    echo -e "  ${YELLOW}$0 preview abc123${NC}"
    echo -e "  ${YELLOW}$0 simple HEAD~5${NC}"
    echo -e "  ${YELLOW}$0 full v1.0.0${NC}"
    echo -e "  ${YELLOW}$0 abc123${NC}  # Tự động chọn"
    echo ""
    echo -e "${CYAN}Tính năng kết hợp:${NC}"
    echo -e "  ${GREEN}✓${NC} Preview chi tiết từ preview-changes.sh"
    echo -e "  ${GREEN}✓${NC} Backup đơn giản từ backup-simple.sh"
    echo -e "  ${GREEN}✓${NC} Backup đầy đủ từ backup-changes.sh"
    echo -e "  ${GREEN}✓${NC} Tự động chọn mode thông minh"
    echo ""
}

# Hàm kiểm tra commit ID
validate_commit() {
    local commit_id=$1
    if ! git rev-parse --verify "$commit_id" >/dev/null 2>&1; then
        echo -e "${RED}❌ Error: Commit ID '$commit_id' không tồn tại${NC}"
        exit 1
    fi
}

# Hàm lấy thông tin commit
get_commit_info() {
    local commit_id=$1
    echo -e "${CYAN}📋 Thông tin Commit:${NC}"
    echo -e "${YELLOW}   Từ commit:${NC} $commit_id ($(git log -1 --format=%s $commit_id))"
    echo -e "${YELLOW}   Đến commit:${NC} $(git rev-parse --short HEAD) ($(git log -1 --format=%s HEAD))"
    echo -e "${YELLOW}   Thời gian:${NC} $(git log -1 --format=%ar $commit_id) → $(git log -1 --format=%ar HEAD)"
    echo ""
}

# Hàm phân tích thay đổi
analyze_changes() {
    local commit_id=$1

    # Đếm số commit
    COMMIT_COUNT=$(git rev-list --count "$commit_id"..HEAD -- "$PROJECT_DIR")
    echo -e "${CYAN}📊 Số commit liên quan:${NC} ${GREEN}${COMMIT_COUNT}${NC}"
    echo ""

    # Danh sách file thay đổi
    echo -e "${CYAN}📁 Danh sách file thay đổi:${NC}"
    CHANGED_FILES=$(git diff --name-status "$commit_id" HEAD -- "$PROJECT_DIR")

    if [ -z "$CHANGED_FILES" ]; then
        echo -e "${YELLOW}   Không có file nào thay đổi${NC}"
        return 1
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

    # Tổng số file
    TOTAL_FILES=$(echo "$CHANGED_FILES" | wc -l)
    echo -e "${CYAN}📈 Tổng số file thay đổi:${NC} ${GREEN}${TOTAL_FILES}${NC}"
    echo ""

    # Thống kê thay đổi
    echo -e "${CYAN}📈 Thống kê thay đổi:${NC}"
    git diff --stat "$commit_id" HEAD -- "$PROJECT_DIR" 2>/dev/null | while read line; do
        echo "   $line"
    done
    echo ""

    return 0
}

# Hàm preview đầy đủ (từ preview-changes.sh)
preview_changes() {
    local commit_id=$1

    echo -e "${BLUE}╔════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${BLUE}║         Preview Thay Đổi - Managing Congregation          ║${NC}"
    echo -e "${BLUE}╚════════════════════════════════════════════════════════════╝${NC}"
    echo ""

    validate_commit "$commit_id"
    get_commit_info "$commit_id"

    if ! analyze_changes "$commit_id"; then
        exit 0
    fi

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
    git shortlog -sn "$commit_id"..HEAD -- "$PROJECT_DIR" 2>/dev/null | head -5 | while read count name; do
        echo -e "   ${GREEN}$name${NC}: $count commits"
    done
    echo ""

    # Ước tính kích thước backup
    echo -e "${CYAN}💾 Ước tính kích thước backup:${NC}"
    TOTAL_SIZE=$(git diff --name-only "$commit_id" HEAD -- "$PROJECT_DIR" | while read file; do
        if [ -f "$file" ]; then
            stat -f%z "$file" 2>/dev/null || stat -c%s "$file" 2>/dev/null || echo 0
        fi
    done | awk '{sum+=$1} END {print sum}')

    if [ -n "$TOTAL_SIZE" ] && [ "$TOTAL_SIZE" -gt 0 ]; then
        SIZE_MB=$(echo "scale=2; $TOTAL_SIZE / 1024 / 1024" | bc 2>/dev/null || echo "0")
        SIZE_KB=$(echo "scale=2; $TOTAL_SIZE / 1024" | bc 2>/dev/null || echo "0")

        if [ "$(echo "$SIZE_MB > 1" | bc 2>/dev/null)" = "1" ]; then
            echo -e "   Kích thước: ~${YELLOW}${SIZE_MB} MB${NC}"
        else
            echo -e "   Kích thước: ~${YELLOW}${SIZE_KB} KB${NC}"
        fi

        # Ước tính sau khi nén
        COMPRESSED_MB=$(echo "scale=2; $SIZE_MB * 0.25" | bc 2>/dev/null || echo "0")
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
    echo -e "   ${CYAN}./backup-unified.sh full $commit_id${NC}"
    echo ""
    echo -e "${GREEN}Backup đơn giản:${NC}"
    echo -e "   ${CYAN}./backup-unified.sh simple $commit_id${NC}"
    echo ""
    echo -e "${GREEN}Xem diff chi tiết:${NC}"
    echo -e "   ${CYAN}git diff $commit_id HEAD -- $PROJECT_DIR${NC}"
    echo ""
}

# Hàm backup đơn giản (từ backup-simple.sh)
backup_simple() {
    local commit_id=$1
    local backup_dir="backup_simple_$(date +%Y%m%d_%H%M%S)"

    echo -e "${BLUE}=== Backup Đơn Giản - Managing Congregation ===${NC}"
    echo -e "${YELLOW}Commit bắt đầu: ${commit_id}${NC}"
    echo -e "${YELLOW}Thư mục backup: ${backup_dir}${NC}"
    echo ""

    validate_commit "$commit_id"

    # Tạo backup
    mkdir -p "$backup_dir"
    echo -e "${GREEN}✓ Đã tạo thư mục backup: ${backup_dir}${NC}"

    # Copy các file đã thay đổi
    echo -e "${BLUE}Đang sao chép các file...${NC}"
    FILE_COUNT=0
    git diff --name-only "$commit_id" HEAD -- "$PROJECT_DIR" | while read file; do
        if [ -f "$file" ]; then
            mkdir -p "$backup_dir/$(dirname "$file")"
            cp "$file" "$backup_dir/$file"
            echo "✓ $file"
            FILE_COUNT=$((FILE_COUNT + 1))
        fi
    done

    # Tạo diff file
    echo -e "${BLUE}Đang tạo file diff...${NC}"
    git diff "$commit_id" HEAD -- "$PROJECT_DIR" > "$backup_dir/changes.diff"
    echo -e "${GREEN}✓ Đã tạo file diff: ${backup_dir}/changes.diff${NC}"

    # Nén
    echo -e "${BLUE}Đang nén backup...${NC}"
    ARCHIVE_NAME="${backup_dir}.tar.gz"
    tar -czf "$ARCHIVE_NAME" "$backup_dir"
    echo -e "${GREEN}✓ Đã tạo file nén: ${ARCHIVE_NAME}${NC}"

    echo ""
    echo -e "${GREEN}=== Backup đơn giản hoàn tất ===${NC}"
    echo -e "File nén: ${YELLOW}${ARCHIVE_NAME}${NC}"
    echo -e "Số file đã backup: ${YELLOW}${FILE_COUNT}${NC}"
    echo ""
}

# Hàm backup đầy đủ (từ backup-changes.sh)
backup_full() {
    local commit_id=$1
    local backup_dir="backup_full_$(date +%Y%m%d_%H%M%S)"

    echo -e "${BLUE}=== Backup Đầy Đủ - Managing Congregation ===${NC}"
    echo -e "${YELLOW}Commit bắt đầu: ${commit_id}${NC}"
    echo -e "${YELLOW}Thư mục backup: ${backup_dir}${NC}"
    echo ""

    validate_commit "$commit_id"

    # Tạo thư mục backup
    mkdir -p "$backup_dir"
    echo -e "${GREEN}✓ Đã tạo thư mục backup: ${backup_dir}${NC}"

    # Lấy danh sách các file đã thay đổi
    echo -e "${BLUE}Đang lấy danh sách file đã thay đổi...${NC}"
    CHANGED_FILES=$(git diff --name-only "$commit_id" HEAD -- "$PROJECT_DIR")

    if [ -z "$CHANGED_FILES" ]; then
        echo -e "${YELLOW}Không có file nào thay đổi trong ${PROJECT_DIR} từ commit ${commit_id}${NC}"
        exit 0
    fi

    # Đếm số file
    FILE_COUNT=$(echo "$CHANGED_FILES" | wc -l)
    echo -e "${GREEN}✓ Tìm thấy ${FILE_COUNT} file đã thay đổi${NC}"
    echo ""

    # Tạo file log
    LOG_FILE="$backup_dir/backup_log.txt"
    echo "Backup Log - $(date)" > "$LOG_FILE"
    echo "Commit bắt đầu: $commit_id" >> "$LOG_FILE"
    echo "Commit kết thúc: $(git rev-parse HEAD)" >> "$LOG_FILE"
    echo "Số file: $FILE_COUNT" >> "$LOG_FILE"
    echo "---" >> "$LOG_FILE"

    # Copy từng file và giữ nguyên cấu trúc thư mục
    COUNTER=0
    echo -e "${BLUE}Đang sao chép các file...${NC}"
    while IFS= read -r file; do
        COUNTER=$((COUNTER + 1))

        # Kiểm tra xem file có tồn tại không
        if [ -f "$file" ]; then
            # Tạo thư mục cha nếu chưa tồn tại
            TARGET_DIR="$backup_dir/$(dirname "$file")"
            mkdir -p "$TARGET_DIR"

            # Copy file
            cp "$file" "$backup_dir/$file"
            echo "[$COUNTER/$FILE_COUNT] ✓ $file" | tee -a "$LOG_FILE"
        else
            echo "[$COUNTER/$FILE_COUNT] ✗ $file (đã bị xóa)" | tee -a "$LOG_FILE"
        fi
    done <<< "$CHANGED_FILES"

    echo ""
    echo -e "${BLUE}Đang tạo các file bổ sung...${NC}"

    # Tạo file diff tổng hợp
    DIFF_FILE="$backup_dir/changes.diff"
    git diff "$commit_id" HEAD -- "$PROJECT_DIR" > "$DIFF_FILE"
    echo -e "${GREEN}✓ Đã tạo file diff: ${DIFF_FILE}${NC}"

    # Tạo file danh sách các file đã thay đổi
    LIST_FILE="$backup_dir/changed_files.txt"
    echo "$CHANGED_FILES" > "$LIST_FILE"
    echo -e "${GREEN}✓ Đã tạo danh sách file: ${LIST_FILE}${NC}"

    # Tạo file thống kê
    STATS_FILE="$backup_dir/statistics.txt"
    echo "=== Thống kê thay đổi ===" > "$STATS_FILE"
    echo "" >> "$STATS_FILE"
    git diff --stat "$commit_id" HEAD -- "$PROJECT_DIR" >> "$STATS_FILE"
    echo -e "${GREEN}✓ Đã tạo file thống kê: ${STATS_FILE}${NC}"

    # Tạo file commit log
    COMMITS_FILE="$backup_dir/commits.txt"
    echo "=== Danh sách commits ===" > "$COMMITS_FILE"
    echo "" >> "$COMMITS_FILE"
    git log --oneline "$commit_id"..HEAD -- "$PROJECT_DIR" >> "$COMMITS_FILE"
    echo -e "${GREEN}✓ Đã tạo danh sách commits: ${COMMITS_FILE}${NC}"

    # Tạo archive
    echo ""
    echo -e "${BLUE}Đang nén backup...${NC}"
    ARCHIVE_NAME="${backup_dir}.tar.gz"
    tar -czf "$ARCHIVE_NAME" "$backup_dir"
    echo -e "${GREEN}✓ Đã tạo file nén: ${ARCHIVE_NAME}${NC}"

    # Tóm tắt
    echo ""
    echo -e "${GREEN}=== Backup đầy đủ hoàn tất ===${NC}"
    echo -e "Thư mục backup: ${YELLOW}${backup_dir}${NC}"
    echo -e "File nén: ${YELLOW}${ARCHIVE_NAME}${NC}"
    echo -e "Số file đã backup: ${YELLOW}${FILE_COUNT}${NC}"
    echo ""
    echo -e "${BLUE}Các file quan trọng:${NC}"
    echo "  - $LOG_FILE (log chi tiết)"
    echo "  - $DIFF_FILE (diff tổng hợp)"
    echo "  - $LIST_FILE (danh sách file)"
    echo "  - $STATS_FILE (thống kê)"
    echo "  - $COMMITS_FILE (danh sách commits)"
    echo ""
    echo -e "${GREEN}✓ Hoàn tất!${NC}"
}

# Hàm tự động chọn mode
auto_select_mode() {
    local commit_id=$1

    # Đếm số file thay đổi
    FILE_COUNT=$(git diff --name-only "$commit_id" HEAD -- "$PROJECT_DIR" | wc -l)

    echo -e "${BLUE}=== Tự Động Chọn Mode ===${NC}"
    echo -e "${YELLOW}Số file thay đổi: ${FILE_COUNT}${NC}"
    echo -e "${YELLOW}Ngưỡng tự động: ${AUTO_THRESHOLD}${NC}"
    echo ""

    if [ "$FILE_COUNT" -le "$AUTO_THRESHOLD" ]; then
        echo -e "${GREEN}→ Chọn mode: SIMPLE (backup nhanh)${NC}"
        echo ""
        backup_simple "$commit_id"
    else
        echo -e "${GREEN}→ Chọn mode: FULL (backup chi tiết)${NC}"
        echo ""
        backup_full "$commit_id"
    fi
}

# Xử lý tham số
case $# in
    0)
        show_help
        exit 0
        ;;
    1)
        # Kiểm tra xem có phải là lệnh help không
        case $1 in
            help|--help|-h)
                show_help
                exit 0
                ;;
            *)
                # Tự động chọn mode
                COMMIT_ID=$1
                auto_select_mode "$COMMIT_ID"
                ;;
        esac
        ;;
    2)
        MODE=$1
        COMMIT_ID=$2

        case $MODE in
            preview)
                preview_changes "$COMMIT_ID"
                ;;
            simple)
                backup_simple "$COMMIT_ID"
                ;;
            full)
                backup_full "$COMMIT_ID"
                ;;
            help|--help|-h)
                show_help
                ;;
            *)
                echo -e "${RED}❌ Error: Mode không hợp lệ '$MODE'${NC}"
                echo ""
                show_help
                exit 1
                ;;
        esac
        ;;
    *)
        echo -e "${RED}❌ Error: Số tham số không đúng${NC}"
        echo ""
        show_help
        exit 1
        ;;
esac
