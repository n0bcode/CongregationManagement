# Implementation Plan

- [x] 1. Create navigation dropdown component

  - Create `resources/views/components/nav-dropdown.blade.php` component
  - Implement Alpine.js state management for dropdown toggle
  - Add props for label, active state, and alignment
  - Implement trigger slot with chevron icon that rotates on open
  - Add ARIA attributes for accessibility (aria-expanded, aria-haspopup)
  - Style dropdown trigger with active state highlighting
  - _Requirements: 1.1, 1.2, 1.3, 7.1_

- [ ]\* 1.1 Write property test for dropdown visibility toggle

  - **Property 1: Dropdown visibility toggle**
  - **Validates: Requirements 1.3**

- [x] 2. Create navigation dropdown menu styling

  - Style dropdown menu container with proper positioning and z-index
  - Add smooth transitions for opening/closing animations
  - Implement hover states for dropdown items
  - Add active state styling for current page within dropdown
  - Ensure dropdown doesn't extend beyond viewport boundaries
  - Add shadow and border styling consistent with design system
  - _Requirements: 1.5, 7.2, 7.3, 7.4_

- [x] 3. Implement click-outside and escape key handlers

  - Add Alpine.js @click.outside directive to close dropdown
  - Implement @keydown.escape handler to close dropdown
  - Ensure focus returns to trigger when closing with escape
  - Test that multiple dropdowns don't interfere with each other
  - _Requirements: 1.5, 8.4_

- [ ]\* 3.1 Write property test for click outside behavior

  - **Property 4: Click outside closes dropdown**
  - **Validates: Requirements 1.5**

- [ ]\* 3.2 Write property test for escape key handler

  - **Property 7: Escape key closes dropdown**
  - **Validates: Requirements 8.4**

- [x] 4. Update dropdown-link component for navigation

  - Extend or modify `resources/views/components/dropdown-link.blade.php`
  - Add active state detection and styling
  - Ensure proper hover and focus states
  - Add support for permission-based visibility
  - _Requirements: 1.4, 7.2_

- [ ]\* 4.1 Write property test for active state propagation

  - **Property 2: Active state propagation**
  - **Validates: Requirements 1.4**

- [x] 5. Implement desktop navigation with dropdown groups

  - Update `resources/views/layouts/navigation.blade.php` desktop section
  - Replace flat navigation links with grouped dropdown structure
  - Implement Management dropdown (Members, Documents)
  - Implement Finance dropdown (Financials, Financial Reports)
  - Implement Reports dropdown (Demographic Reports)
  - Implement System dropdown (Audit Logs) with permission check
  - Keep Dashboard as standalone link
  - _Requirements: 2.1, 2.2, 2.4, 3.1, 3.2, 3.3, 4.1, 4.2, 4.3, 5.1, 5.2, 5.4_

- [ ]\* 5.1 Write property test for permission-based visibility

  - **Property 3: Permission-based visibility**
  - **Validates: Requirements 2.3, 5.3**

- [x] 6. Implement keyboard navigation support

  - Add tabindex and focus management to dropdown triggers
  - Implement Enter/Space key handlers to open dropdowns
  - Ensure Tab key navigates through dropdown items sequentially
  - Add visible focus indicators to all interactive elements
  - Test keyboard navigation flow through entire navigation bar
  - _Requirements: 8.1, 8.2, 8.3, 8.5_

- [ ]\* 6.1 Write property test for keyboard navigation

  - **Property 6: Keyboard navigation focus management**
  - **Validates: Requirements 8.3**

- [x] 7. Create mobile navigation accordion component

  - Create accordion-style expansion for mobile grouped items
  - Implement Alpine.js state for section expansion/collapse
  - Add chevron icon that rotates when section expands
  - Style section headers and content with proper spacing
  - Ensure smooth collapse/expand animations
  - _Requirements: 6.1, 6.2_

- [ ]\* 7.1 Write property test for mobile accordion

  - **Property 5: Mobile accordion expansion**
  - **Validates: Requirements 6.2**

- [x] 8. Implement mobile responsive navigation

  - Update `resources/views/layouts/navigation.blade.php` mobile section
  - Replace flat mobile links with accordion sections
  - Implement Management section with Members and Documents
  - Implement Finance section with Financials and Financial Reports
  - Implement Reports section with Demographic Reports
  - Implement System section with Audit Logs (permission-based)
  - Keep Dashboard as standalone link
  - Ensure tap-outside closes mobile menu
  - _Requirements: 6.1, 6.2, 6.3, 6.4_

- [x] 9. Add visual indicators and animations

  - Implement chevron rotation animation for dropdown triggers
  - Add smooth transitions for dropdown open/close
  - Ensure active parent dropdown has distinct styling
  - Add hover state transitions for dropdown items
  - Test animations perform smoothly on mobile devices
  - _Requirements: 7.1, 7.2, 7.3, 7.4_

- [ ]\* 9.1 Write property test for visual indicator rotation

  - **Property 8: Visual indicator rotation**
  - **Validates: Requirements 7.1**

- [x] 10. Implement permission-based dropdown visibility

  - Add permission checks for Documents link in Management dropdown
  - Hide entire System dropdown if user lacks audit log permissions
  - Ensure empty dropdowns (all items hidden) hide the parent dropdown
  - Test with different user roles (admin, editor, viewer)
  - _Requirements: 2.3, 5.3_

- [x] 11. Add ARIA attributes and accessibility features

  - Add aria-expanded to all dropdown triggers
  - Add aria-haspopup="true" to dropdown triggers
  - Add role="menu" to dropdown containers
  - Add role="menuitem" to dropdown links
  - Test with screen readers (NVDA, JAWS, VoiceOver)
  - Verify keyboard navigation works with screen readers
  - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_

- [ ]\* 11.1 Write unit tests for accessibility attributes

  - Test that ARIA attributes are correctly rendered
  - Test that role attributes are present
  - Test that screen reader announcements are appropriate
  - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_

- [x] 12. Test responsive behavior across devices

  - Test desktop dropdown interactions in Chrome, Firefox, Safari
  - Test mobile accordion on iOS Safari and Chrome
  - Test tablet view transitions between mobile and desktop layouts
  - Verify touch interactions don't conflict with scroll gestures
  - Test with different screen sizes and orientations
  - _Requirements: 6.1, 6.2, 6.3, 6.4_

- [ ]\* 12.1 Write integration tests for responsive navigation

  - Test navigation rendering with different user roles
  - Test mobile/desktop layout switching
  - Test permission-based visibility across roles
  - _Requirements: 6.1, 6.2, 6.3, 6.4_

- [x] 13. Checkpoint - Ensure all tests pass

  - Ensure all tests pass, ask the user if questions arise.

- [x] 14. Update navigation translations

  - Add translation keys for new dropdown labels (Management, Finance, Reports, System)
  - Update language files (en, vi) with appropriate translations
  - Verify all navigation labels display correctly in both languages
  - _Requirements: 1.1, 2.1, 3.1, 4.1, 5.1_

- [x] 15. Performance optimization and cleanup

  - Verify dropdown animations use CSS transforms for performance
  - Add will-change CSS property for mobile accordion animations
  - Test navigation performance on low-end mobile devices
  - Remove any unused CSS or JavaScript code
  - Clear view cache and test in production mode
  - _Requirements: 7.4_

- [x] 16. Final testing and documentation

  - Test complete navigation flow with all user roles
  - Verify graceful degradation if JavaScript is disabled
  - Document new component usage in code comments
  - Update any relevant developer documentation
  - Create rollback plan documentation
  - _Requirements: All_

- [x] 17. Final Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.
