#!/bin/bash

# Script đơn giản để backup thay đổi trong managing-congregation
# Sử dụng: ./backup-simple.sh <commit-id>

if [ -z "$1" ]; then
    echo "Error: Vui lòng cung cấp commit ID"
    echo "Sử dụng: $0 <commit-id>"
    exit 1
fi

COMMIT_ID=$1
BACKUP_DIR="backup_$(date +%Y%m%d_%H%M%S)"

# Kiểm tra commit
if ! git rev-parse --verify "$COMMIT_ID" >/dev/null 2>&1; then
    echo "Error: Commit ID '$COMMIT_ID' không tồn tại"
    exit 1
fi

# Tạo backup
mkdir -p "$BACKUP_DIR"

# Copy các file đã thay đổi
git diff --name-only "$COMMIT_ID" HEAD -- managing-congregation | while read file; do
    if [ -f "$file" ]; then
        mkdir -p "$BACKUP_DIR/$(dirname "$file")"
        cp "$file" "$BACKUP_DIR/$file"
        echo "✓ $file"
    fi
done

# Tạo diff file
git diff "$COMMIT_ID" HEAD -- managing-congregation > "$BACKUP_DIR/changes.diff"

# Nén
tar -czf "${BACKUP_DIR}.tar.gz" "$BACKUP_DIR"

echo ""
echo "Backup hoàn tất: ${BACKUP_DIR}.tar.gz"
