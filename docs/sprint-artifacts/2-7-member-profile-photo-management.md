# Story 2.7: Member Profile Photo Management

Status: in-progress

## Story

As a **Secretary or Community Director**,
I want **to upload, update, and delete a profile photo for a member**,
so that **we can easily identify members in the system and on generated cards**.

## Acceptance Criteria

1.  Given I am on the member's profile page, I can see their current profile photo or a default placeholder if none exists.
2.  When I click on the photo or an "Update Photo" button, I can select an image file (JPG, PNG) to upload.
3.  The system validates the image file (max size 2MB, allowed types: jpg, jpeg, png).
4.  Upon successful upload, the new photo replaces the old one (if any) and is displayed immediately.
5.  The old photo file should be deleted from storage to save space.
6.  I can delete the current photo, reverting the profile to the default placeholder.
7.  The photo is stored securely but accessible for public/authenticated views as needed (e.g., `public` disk or `storage` link).

## Tasks / Subtasks

- [ ] **Backend: Photo Storage Logic**
  - [ ] Create `UpdateMemberPhotoRequest` for validation (image, max:2048, mimes:jpeg,png).
  - [ ] Add `profile_photo_path` column to `members` table (if not already present - check migration).
  - [ ] Update `Member` model to include `profile_photo_path` in fillable.
  - [ ] Implement `updatePhoto` and `deletePhoto` methods in `MemberController` (or a dedicated `MemberPhotoController`).
  - [ ] Ensure file storage uses the `public` disk.
- [ ] **Frontend: Photo Management UI**
  - [ ] Update `show.blade.php` to display the photo.
  - [ ] Add a form/modal to upload a new photo.
  - [ ] Add a button/action to delete the current photo.
- [ ] **Testing**
  - [ ] Feature test: Upload valid photo, upload invalid photo, delete photo.
  - [ ] Verify file existence in storage mock.

## Dev Notes

- **Storage:** Use Laravel's `Storage` facade with the `public` disk. Ensure `php artisan storage:link` has been run (it usually is in Sail).
- **Column:** Check if `profile_photo_path` exists. If not, create a migration.
- **UI:** Use a simple file input or a click-to-upload overlay on the image.

### References

- [Source: `plans/origin_require.txt` - 1.1 Hồ sơ cá nhân: hình ảnh]
