# Phase 6 Slice F — Operator feedback

> **Parent:** [Admin98_Phase6_Admin_Windows.md](./Admin98_Phase6_Admin_Windows.md)  
> **Status:** done

## Chosen UX (Win98-like)

| Kind | UI | Sound |
|------|-----|--------|
| Load / save / verify / assign / unassign **failure** | Error `MessageDialog` | chord |
| Session **401** | Same dialog → OK → `/login` | chord |
| **Success** | Status bar middle field only | none |
| Idle | Count left; selection name / hint middle | — |

Success text auto-clears after 4s and clears on new selection / mutate / error. No success MessageDialog.

## Landed

- `statusMessage` / `onClearStatusMessage` on Sites + Hosts windows; dialog owns errors (not status bar).
- `formError` opens MessageDialog even when the form is closed (Assign/Unassign).
- `AdminDesktop` flashes short English success strings after mutate OK.
- Stories: `StatusMessage`, Sites `Form Error When Closed`.
