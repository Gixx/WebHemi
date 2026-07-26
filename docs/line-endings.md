# Line endings (LF only)

All WebHemi repositories store text files with **Unix LF** (`\n`) only. CRLF is rejected.

## Repo settings (already applied locally)

Each repo uses:

| Setting | Value | Effect |
|---------|-------|--------|
| `core.autocrlf` | `false` | No Windows conversion on checkout/commit |
| `core.eol` | `lf` | Prefer LF |
| `core.safecrlf` | `true` | Refuse commits that would introduce mixed/broken CR conversion |
| `core.hooksPath` | `.githooks` | Repo-managed hooks |

Plus:

- **`.gitattributes`** — `* text=auto eol=lf` (and explicit patterns)
- **`.editorconfig`** — `end_of_line = lf`
- **`.githooks/pre-commit`** — fails if any staged **text** file still contains `\r` (binaries such as `.woff`, `.gif`, `.png` are skipped; they may contain legitimate CR bytes)

After clone on a new machine:

```bash
git config --local core.autocrlf false
git config --local core.eol lf
git config --local core.safecrlf true
git config --local core.hooksPath .githooks
```

(Or rely on documenting that teammates run the same; `hooksPath` is local and not shared via the repo files alone — the `.githooks` directory is committed, but enabling it requires the `core.hooksPath` config once.)

## If CRLF sneaks back in

```bash
# working tree
find . -type f ! -path './.git/*' ! -path './vendor/*' ! -path './node_modules/*' \
  -exec grep -l $'\r' {} \; | xargs -r sed -i 's/\r$//'

# re-apply attributes after fixing
git add --renormalize .
```

## Editors (WSL / Windows)

- Prefer opening the project from **WSL** (Cursor/VS Code Remote-WSL).
- If editing from Windows on a WSL path, ensure EditorConfig is enabled and `files.eol` is `\n`.
