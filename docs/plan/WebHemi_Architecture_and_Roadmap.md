# WebHemi: Dual-Engine CMS Architecture & Implementation Roadmap (Multi-Repo Version)

Welcome to **WebHemi**, an ambitious, educational, and production-ready dual-engine CMS. This project is built upon a revolutionary premise: separating the design system (the visual "truth") from the backend rendering technologies. 

This specification defines a **Multi-Repository** architecture, allowing each part to live in its own clean environment, making distribution and installation exceptionally simple for end-users:
1. **webhemi-ui**: The Storybook and React core, distributed via NPM.
2. **webhemi-php**: The Symfony 7 + Twig monolithic CMS application (WebHemi.PHP).
3. **webhemi-js**: The Next.js 15 + Payload CMS 3.0 application (WebHemi.JS).

---

## 1. High-Level Multi-Repo Architecture

To provide maximum ease of use for end-users, WebHemi.PHP does not require Node.js, Next.js, or Storybook dependencies in production. It simply pulls down compiled, ready-to-run assets.

```
                       +----------------------------------+
                       |      [Repo 1: webhemi-ui]        |
                       |   React Components + Storybook   |
                       +----------------------------------+
                                        |
                             (Publish to NPM Registry)
                                        |
                +-----------------------+-----------------------+
                |                                               |
                v                                               v
    +-----------------------+                       +-----------------------+
    | [Repo 2: webhemi-php] |                       | [Repo 3: webhemi-js]  |
    |                       |                       |                       |
    |    Symfony Monolith   |                       |    Next.js Frontend   |
    |   Pulls built assets  |                       |   Pulls npm package   |
    |   via AssetMapper /   |                       |   Runs local Payload  | 
    |   Importmap (No Node) |                       |   CMS admin portal    |
    +-----------------------+                       +-----------------------+
```

---

## 2. Repository Schemes

### Repository 1: `webhemi-ui` (Design System)
Contains the core UI views and components. Published as an NPM package (`@webhemi/ui`).
```text
webhemi-ui/
├── .storybook/          # Storybook configuration
├── src/
│   ├── components/      # Button, Card, Hero, etc.
│   └── index.ts         # Library exports
├── package.json         # Build scripts to compile tsx -> production-ready JS/CSS
└── tailwind.config.js
```

### Repository 2: `webhemi-php` (WebHemi.PHP)
Designed for PHP-first users. **Zero JS runtime overhead**. Just PHP, Twig, and AssetMapper.
```text
webhemi-php/
├── config/
├── importmap.php        # Locks and registers the compiled @webhemi/ui assets
├── src/
│   ├── Controller/
│   └── Entity/
├── templates/
│   └── base.html.twig   # Renders @webhemi/ui via {{ react_component(...) }}
└── composer.json
```

### Repository 3: `webhemi-js` (WebHemi.JS)
The headless React-centric JS stack.
```text
webhemi-js/
├── app/                 # Next.js App Router with embedded Payload 3.0
├── collections/         # Payload database schemas
├── package.json         # References @webhemi/ui from NPM registry
└── payload.config.ts
```

---

## 3. Step-by-Step Implementation Roadmap

### Phase 1: Build & Distribute `@webhemi/ui`
*Objective: Setup the UI repository, test it with Storybook, compile it, and publish it.*

1. **Initialize UI Project**:
   * Setup Tailwind, React, and TypeScript.
   * Install Storybook: `npx storybook@latest init`.
2. **Write Components**:
   * Write modular, customizable atoms (e.g., `Button.tsx`).
3. **Setup compilation & release**:
   * Use a bundler like `tsup` or `vite` to output clean ESM (`dist/index.js`) and CSS (`dist/index.css`) files.
   * Publish to NPM: `npm publish --access public` (or a private registry).

### Phase 2: Building WebHemi.PHP (Zero-Node Production Deployment)
*Objective: Configure WebHemi.PHP so users can set it up instantly with Composer and zero JS infrastructure.*

1. **Install Symfony**:
   * Spin up a skeleton: `composer create-project symfony/skeleton webhemi-php`.
2. **Configure AssetMapper & Symfony UX React**:
   * Install: `composer require symfony/ux-react`.
   * This utilizes Symfony's AssetMapper (No Webpack/Node.js required!).
3. **Reference the Distributed Design System**:
   * Symfony's `importmap.php` allows importing your NPM module directly using a CDN (like JSPM or UNPKG):
     ```bash
     php bin/console importmap:require @webhemi/ui
     ```
   * Now, the built Javascript file is linked cleanly without needing any node_modules folder.
4. **User Installation Pipeline**:
   * When a client clones your `webhemi-php` project, they only need to run:
     ```bash
     composer install
     php bin/console importmap:install
     ```
   * Your CMS is immediately operational, loading React components natively from Twig.

### Phase 3: Building WebHemi.JS (The headless JS CMS)
*Objective: Create the Next.js and Payload CMS integration that consumes the NPM package.*

1. **Create Next.js Application**:
   * Create the app: `npx create-next-app@latest webhemi-js`.
2. **Install @webhemi/ui**:
   * Run `npm install @webhemi/ui` inside the project to fetch the same UI modules.
3. **Integrate Payload CMS 3.0**:
   * Add `@payloadcms/next` to run the database admin UI inside the Next app directory.
4. **Render Pages**:
   * Render views by directly importing React nodes: `import { Button } from '@webhemi/ui';`.

### Phase 4: Developer Workflow (Local Symlinks)
*Objective: Make sure you don't have to publish to NPM for every single change during active development.*

1. **Local JS Link**:
   * In `webhemi-ui`, run `npm link`.
   * In `webhemi-js`, run `npm link @webhemi/ui`. Now local changes in UI instantly reflect in Next.js.
2. **Local PHP Link**:
   * For local development on Symfony, you can configure your assetmapper directory helper to point directly to the local folder of `webhemi-ui/dist` instead of the remote CDN.

### Phase 5: Distribution & Comparison
* 1. Package WebHemi.PHP as a clean zip/git release with a database installer (wizard: locale, DB, primary domain → protected main site/host + Hello world + path-based `/admin`). Detail: [`Installer_and_Protected_Base_Site.md`](./Installer_and_Protected_Base_Site.md).
* 2. Verify that WebHemi.PHP production environments require absolutely zero Node.js setups.
* 3. Audit performance metrics between both stacks.

---

## 4. Architectural Advantages of the Multi-Repo Approach

* **Ultimate PHP Performance**: WebHemi.PHP remains highly performant. Symfony renders HTML skeletons instantly, and AssetMapper downloads the cached, compiled UI script on the client side. No slow Webpack compilations on your production VPS.
* **Complete Tech Separation**: Next.js users only download JS files, PHP users only deal with Symfony code, while you maintain a unified, clean, and singular source of design truth in `webhemi-ui`.
