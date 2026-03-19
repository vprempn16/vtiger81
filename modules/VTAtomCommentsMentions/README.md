# VTAtomCommentsMentions

This module adds **Mention tagging** (e.g., `@username`) directly into standard module comments widgets.

## Flow of Work
1. **Event Hook**: Registers `CommentMentionSendMail` on the `ModComments` `vtiger.entity.aftersave` broadcasts trigger.
2. **License Verify**: Validates active state using core metadata controllers.
3. **Parse Mentions**: Explodes strings matching `@` prefix iterates lookups securely.
4. **Dispatches**: Sends layout emails, and triggers `VDNotifierPro` streams triggers.

## Fixes Provided
- Fixed undefined `$request` crash inside silent Event Hook.
- Fixed `SaveLicense` `.serialize()` query payloads decoding.
- Integrated `VDNotifierPro` save model trigger loops.
- **File Updated for Notification Hooks**: `modules/VTAtomCommentsMentions/CommentMentionSendMail.php`
