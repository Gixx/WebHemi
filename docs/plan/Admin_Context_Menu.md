# Admin context menu (+ optional menu icons)

> **Status:** slices A–B done (Storybook chrome + ExplorerMenuBar icons); C (product wiring) later.  
> **Parent:** [CURRENT.md](./CURRENT.md) · related [FileExplorer_Window.md](./FileExplorer_Window.md) (MenuBar extract / Phase 14).  
> **Language:** English only.

## Intent

Ship a Win98-style **context menu** (right-click) as an Admin chrome atom, Storybook-first. Product surfaces (desktop, Sites/Hosts rows, explorer, taskbar, …) each supply their own item tree later — that wiring is out of scope for the visual slice.

Also lock a gap from the explorer **window menubar**: every menu item may carry an **optional icon**. That belongs on a **shared menu-item model**, not only on context menus.

## Locked product rules

| Rule | Detail |
|------|--------|
| Optional icon | Non-checkable command items **may** have an icon; omission is valid |
| Checkable items | Toggle / radio rows that show a **checkmark** (`menuitemcheckbox` / `menuitemradio` / `checked`) **never** have an icon |
| Leading gutter | **One** leading column: either **check** (✓) **or** **icon** — not both stacked. If **any** row is checkable → check column (command rows get an empty check cell; no icons). Else if any command has an icon → icon column. Else none |
| Pure / mixed check menus | View-style (check/radio, optionally plain commands like Refresh) → check column only; **no** icon column |
| Icon size | Small toolbar/menu glyph (~16×16), not desktop `SystemIcon` (32+) |
| Separators | No icon / no check |
| Submenus | Allowed in the model (chevron); nested open behavior can be stubbed in v1 stories |
| Disabled / checked | Same semantics as `ExplorerMenuBar` today |
| Access keys | Optional underline via existing `accessKey` helper where the surface provides one |
| Native menu | Suppress browser default where we own `contextmenu` (product wiring); Storybook demos use explicit open |

## Shared item model (target)

Declarative items used by **context menu** and later by extracted **MenuBar** / updated `ExplorerMenuBar`:

```ts
type AdminMenuItem =
  | { kind: 'separator'; id: string }
  | {
      kind: 'item';
      id: string;
      label: string;
      accessKey?: string;
      /** Command items only — forbidden / ignored when checkable. */
      icon?: ReactNode | { src: string; alt?: string };
      disabled?: boolean;
      /** When set (or role is checkbox/radio), item is checkable: check gutter, no icon. */
      checked?: boolean;
      role?: 'menuitem' | 'menuitemradio' | 'menuitemcheckbox';
      children?: AdminMenuItem[]; // submenu
      onSelect?: () => void;
    };
```

Exact `icon` shape can be `ReactNode` first (Storybook passes `<img>` / SVG); a typed glyph map can come later.

Invariant: `checked` / checkbox / radio ⇒ no `icon`. Implementation may type this as a discriminated union later.

## Work slices

### A — Storybook chrome — **done**

1. `Admin/Atoms/ContextMenu` (or `MenuPopup` if we want one popup used by both menubar + context).
2. Stories: command menu with mixed icons / no icons; **checkable-only** menu (✓, no icon column); disabled / separators; 2–3 fixture trees (desktop empty, Sites row, explorer file) as **static** demos.
3. Positioning helper stub: fixed coords / `openAt(x,y)` for stories; collision clamp optional.
4. Docs note: window menubar icon gap; model shared.

### B — Backfill window menu icons — **done**

1. Extend `ExplorerMenuBar` item type + CSS icon column (File → Open, Edit cut/copy/paste, etc. where glyphs already exist under `admin/assets/icons/toolbar/`).
2. Prefer extracting shared popup/item renderer if duplication hurts; otherwise thin duplicate CSS with same class names until Phase 14 extract.

### C — Product wiring (later, per surface)

`onContextMenu` → select target if needed → open menu with surface-specific items → dismiss on outside click / Escape / scroll. Not part of A.

## Out of scope for A

- Full desktop/Sites/Hosts/explorer integration
- Dynamic permission-filtered menus from API
- Keyboard-only “menu key” on every control (nice-to-have after A)
- Replacing Start menu (separate chrome)

## CURRENT placement

Add as a small chrome slice when scheduling (does not block Phase 3 MSW / CP windows). Natural neighbor of Phase 14 MenuBar extract; icons on menubar can land in B even before full extract.

## Done when (slice A)

Implemented: `webhemi-ui/src/admin/chrome/ContextMenu/`, `MenuPopup/`, `styles/chrome/_menu.scss`.

- Storybook shows context menu with optional icons and reserved icon column (command menus)
- Separate story (or section) for checkable-only menu: check column, **no** icon column
- Item model documented here and in component docs
- No requirement that every command item has an icon; checkable items never show icons
