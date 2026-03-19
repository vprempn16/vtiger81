# VDNotifierPro

This module pushes **Global Notifications** feeds directly up to user header/dashboard widgets layout controllers.

## Flow of Work
1. **Event Hooks**: Registers universal entity triggers on generic creation/updates.
2. **Push Delivery**: Wraps updates in `VDNotifierPro_Record_Model` buffers dispatching save lookups.

## New Feature Added
- **Mention Integration**: Integrated with `VTAtomCommentsMentions` layout hooks! Mentions now trigger direct notifications records to corresponding Tag targets universally above comments.
