# Custom Confirmation Modal Implementation

Replace native `confirm()` dialogs with a custom, glassmorphism-styled modal that centers on the screen, adhering to the project's design language.

## Proposed Changes

### [Frontend]
#### [MODIFY] [app.css](file:///h:/xampp/htdocs/ucprukdb/resources/css/app.css)
- Add styles for the modal overlay and container (glassmorphism effect).
- Add animations for modal appearance (fade/scale).

#### [MODIFY] [layouts/app.blade.php](file:///h:/xampp/htdocs/ucprukdb/resources/views/layouts/app.blade.php)
- Add a hidden modal structure at the bottom of the layout.
- Implement a global JavaScript helper `window.confirmAction` to show the modal and return a Promise or use a callback.

#### [MODIFY] [client-bio/index.blade.php](file:///h:/xampp/htdocs/ucprukdb/resources/views/client-bio/index.blade.php)
#### [MODIFY] [assessments/index.blade.php](file:///h:/xampp/htdocs/ucprukdb/resources/views/assessments/index.blade.php)
#### [MODIFY] [wheelchairs/index.blade.php](file:///h:/xampp/htdocs/ucprukdb/resources/views/wheelchairs/index.blade.php)
- Update delete forms to call the new custom modal function.

## Verification Plan

### Manual Verification
1. Click any delete button.
2. Verify that a beautiful glassmorphism modal appears in the middle of the screen.
3. Verify that the "Cancel" button closes the modal.
4. Verify that the "Confirm" button triggers the corresponding form submission.
5. Check responsiveness on mobile devices.
