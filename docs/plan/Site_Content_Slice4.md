# Phase 10 Slice 4 — Document editor (Lexical)

> **Status:** implemented.  
> **Parent:** [Site_Content_Model.md](./Site_Content_Model.md) · [CURRENT.md](./CURRENT.md) Phase 10.  
> **Language:** English only.

## Goal

Open site **documents** in a rich editor window: standard rich text plus **custom blocks** that appear as **placeholders** in the canvas and get configured in a **separate dialog/window** (Win98 admin chrome). Public theme rendering of blocks stays Slice 5+ / theme work.

## Locked decisions

| Topic | Choice |
|-------|--------|
| Editor | **[Lexical](https://github.com/facebook/lexical)** (`lexical` + `@lexical/react`) — MIT, React-first |
| Not chosen | TipTap (not free for our licensing needs); CKEditor/TinyMCE (weaker custom-block + separate-settings UX) |
| Storage | Persist Lexical **editor state JSON** on `content_node.body` (not HTML-as-source-of-truth) |
| Custom blocks | `DecoratorBlockNode`: canvas = placeholder chip; Edit… → config dialog |
| First custom block | **Accordion** (MVP schema below); more block types later |
| Admin chrome | Lexical `ContentEditable` + thin toolbar under `[data-wh-theme="admin"]`; no Lexical playground skin |
| Public HTML | Slice 5: theme/Twig renders from stored JSON — [Site_Content_Slice5.md](./Site_Content_Slice5.md) |

## What shipped

| Area | Detail |
|------|--------|
| UI brick | `webhemi-ui/src/admin/bricks/DocumentEditor/` — window, canvas, toolbar, accordion node + settings dialog |
| Shell | `document-editor` kind; `documentEditorWindowId(siteId, nodeId)`; open from explorer document leaf |
| API client | `GET/PATCH …/sites/{id}/nodes/{id}` for load/save `title` + `body` |
| PHP | `LexicalDocumentBody` validates document `body` on PATCH (empty or JSON object with `root`); 422 on invalid |
| MSW / Storybook | Node get/patch with body map; `DocumentEditorWindow` stories; demo forest ids normalized to `node-*` |

## Persistence

| Approach | Decision |
|----------|----------|
| Canonical payload | Lexical `SerializedEditorState` JSON string in `body` |
| Empty document | Empty / null / legacy non-JSON → empty editor on open |
| Validation | Server JSON + `root` object check when `kind === document` |

## Accordion MVP

Accordion: Twig partial from `wh-accordion` props. Public markup: [Site_Content_Slice5.md](./Site_Content_Slice5.md).

## Out of scope (unchanged)

- TipTap / paid editor stacks  
- Public accordion (and other block) theme rendering → **Slice 5**  
- Nested Lexical inside accordion item bodies  
- Collaborative editing, comments  
- Media embed block  

## Related

- [Site_Content_Model.md](./Site_Content_Model.md) — document leaf + Phase 10 slices  
- [Site_Content_Slice2.md](./Site_Content_Slice2.md) — New Page creates empty document  
- [FileExplorer_Window.md](./FileExplorer_Window.md) — open document behavior  
- [Frontend_Sites_and_Themes.md](./Frontend_Sites_and_Themes.md) — public render later  
