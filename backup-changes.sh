#!/bin/bash

# Script để sao lưu các thay đổi trong managing-congregation từ một commit cụ thể
# Sử dụng: ./backup-changes.sh <commit-id>
# Ví dụ: ./backup-changes.sh abc123def

set -e  # Dừng script nếu có lỗi

# Màu sắc cho output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Kiểm tra tham số
if [ -z "$1" ]; then
    echo -e "${RED}Error: Vui lòng cung cấp commit ID${NC}"
    echo "Sử dụng: $0 <commit-id>"
    echo "Ví dụ: $0 abc123def"
    exit 1
fi

COMMIT_ID=$1
BACKUP_DIR="backup_$(date +%Y%m%d_%H%M%S)"
PROJECT_DIR="managing-congregation"

echo -e "${BLUE}=== Backup Script cho Managing Congregation ===${NC}"
echo -e "${YELLOW}Commit bắt đầu: ${COMMIT_ID}${NC}"
echo -e "${YELLOW}Thư mục backup: ${BACKUP_DIR}${NC}"
echo ""

# Kiểm tra xem commit có tồn tại không
if ! git rev-parse --verify "$COMMIT_ID" >/dev/null 2>&1; then
    echo -e "${RED}Error: Commit ID '$COMMIT_ID' không tồn tại${NC}"
    exit 1
fi

# Tạo thư mục backup
mkdir -p "$BACKUP_DIR"
echo -e "${GREEN}✓ Đã tạo thư mục backup: ${BACKUP_DIR}${NC}"

# Lấy danh sách các file đã thay đổi trong managing-congregation
echo -e "${BLUE}Đang lấy danh sách file đã thay đổi...${NC}"
CHANGED_FILES=$(git diff --name-only "$COMMIT_ID" HEAD -- "$PROJECT_DIR")

if [ -z "$CHANGED_FILES" ]; then
    echo -e "${YELLOW}Không có file nào thay đổi trong ${PROJECT_DIR} từ commit ${COMMIT_ID}${NC}"
    exit 0
fi

# Đếm số file
FILE_COUNT=$(echo "$CHANGED_FILES" | wc -l)
echo -e "${GREEN}✓ Tìm thấy ${FILE_COUNT} file đã thay đổi${NC}"
echo ""

# Tạo file log
LOG_FILE="$BACKUP_DIR/backup_log.txt"
echo "Backup Log - $(date)" > "$LOG_FILE"
echo "Commit bắt đầu: $COMMIT_ID" >> "$LOG_FILE"
echo "Commit kết thúc: $(git rev-parse HEAD)" >> "$LOG_FILE"
echo "Số file: $FILE_COUNT" >> "$LOG_FILE"
echo "---" >> "$LOG_FILE"

# Copy từng file và giữ nguyên cấu trúc thư mục
COUNTER=0
echo -e "${BLUE}Đang sao chép các file...${NC}"
while IFS= read -r file; do
    COUNTER=$((COUNTER + 1))
    
    # Kiểm tra xem file có tồn tại không (có thể đã bị xóa)
    if [ -f "$file" ]; then
        # Tạo thư mục cha nếu chưa tồn tại
        TARGET_DIR="$BACKUP_DIR/$(dirname "$file")"
        mkdir -p "$TARGET_DIR"
        
        # Copy file
        cp "$file" "$BACKUP_DIR/$file"
        echo "[$COUNTER/$FILE_COUNT] ✓ $file" | tee -a "$LOG_FILE"
    else
        echo "[$COUNTER/$FILE_COUNT] ✗ $file (đã bị xóa)" | tee -a "$LOG_FILE"
    fi
done <<< "$CHANGED_FILES"

echo ""
echo -e "${BLUE}Đang tạo file diff...${NC}"

# Tạo file diff tổng hợp
DIFF_FILE="$BACKUP_DIR/changes.diff"
git diff "$COMMIT_ID" HEAD -- "$PROJECT_DIR" > "$DIFF_FILE"
echo -e "${GREEN}✓ Đã tạo file diff: ${DIFF_FILE}${NC}"

# Tạo file danh sách các file đã thay đổi
LIST_FILE="$BACKUP_DIR/changed_files.txt"
echo "$CHANGED_FILES" > "$LIST_FILE"
echo -e "${GREEN}✓ Đã tạo danh sách file: ${LIST_FILE}${NC}"

# Tạo file thống kê
STATS_FILE="$BACKUP_DIR/statistics.txt"
echo "=== Thống kê thay đổi ===" > "$STATS_FILE"
echo "" >> "$STATS_FILE"
git diff --stat "$COMMIT_ID" HEAD -- "$PROJECT_DIR" >> "$STATS_FILE"
echo -e "${GREEN}✓ Đã tạo file thống kê: ${STATS_FILE}${NC}"

# Tạo file commit log
COMMITS_FILE="$BACKUP_DIR/commits.txt"
echo "=== Danh sách commits ===" > "$COMMITS_FILE"
echo "" >> "$COMMITS_FILE"
git log --oneline "$COMMIT_ID"..HEAD -- "$PROJECT_DIR" >> "$COMMITS_FILE"
echo -e "${GREEN}✓ Đã tạo danh sách commits: ${COMMITS_FILE}${NC}"

# Tạo archive (nén)
echo ""
echo -e "${BLUE}Đang nén backup...${NC}"
ARCHIVE_NAME="${BACKUP_DIR}.tar.gz"
tar -czf "$ARCHIVE_NAME" "$BACKUP_DIR"
echo -e "${GREEN}✓ Đã tạo file nén: ${ARCHIVE_NAME}${NC}"

# Tóm tắt
echo ""
echo -e "${GREEN}=== Backup hoàn tất ===${NC}"
echo -e "Thư mục backup: ${YELLOW}${BACKUP_DIR}${NC}"
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
