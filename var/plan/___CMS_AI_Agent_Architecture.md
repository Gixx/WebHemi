# System Architecture Document: AI-Assisted Content Management System & Desktop Client

**Version:** 1.0.0  
**Date:** August 11, 2026  
**Status:** Architecture Proposal & Specification  

---

## Executive Summary

This document outlines the architecture for a modern, hybrid Content Management System (CMS) and its companion AI-powered Desktop Application. 

The ecosystem consists of two core components:
1. **Headless / API-driven CMS Backend:** Built on PHP and Symfony, acting as the single source of truth for articles, media assets, and user data.
2. **Cross-Platform Desktop Client:** Built on C# (.NET) and Avalonia UI, serving as an intelligent authoring workstation. The desktop app leverages local or cloud-based AI agents to research, generate, format, and illustrate blog posts before publishing them to the backend via REST/GraphQL APIs.

By decoupling the AI generation workflow from the web server, the system achieves maximum stability, eliminates server-side timeout bottlenecks, eliminates provider API costs for the platform operator through a *Bring Your Own Key* (BYOK) model, and delivers a responsive user experience across Windows, Linux, and macOS.

---

## 1. Architectural Overview & System Topology

```
+-----------------------------------------------------------------------------------+
|                            LOCAL DESKTOP WORKSTATION                              |
|                                                                                   |
|  +-----------------------------------------------------------------------------+  |
|  |                        C# / Avalonia UI Desktop Client                      |  |
|  |                                                                             |  |
|  |  +-------------------+     +--------------------+     +------------------+  |  |
|  |  |   Configuration   |     |    AI Orchestrator |     |   Local Storage  |  |  |  |
|  |  |   & BYOK Storage  |     | (Semantic Kernel)  |     | (SQLite Cache)   |  |  |  |
|  |  +---------+---------+     +---------+----------+     +--------+---------+  |  |  |
|  +------------|-------------------------|-------------------------|------------+  |
+---------------|-------------------------|-------------------------|---------------+
                |                         |                         |
                | (API Keys)              | (LLM/Image Prompts)     | (JSON/Multipart Upload)
                v                         v                         v
+-------------------------+   +-----------------------+   +-------------------------+
|   External AI Providers |   |   Web Search APIs     |   |    Symfony API Backend  |
|                         |   |                       |   |                         |
|  * OpenAI (GPT-4o/DALL-E|   |  * Tavily / Exa API   |   |  * API Platform / REST  |
|  * Groq Cloud           |   |  * DuckDuckGo API     |   |  * Doctrine ORM         |
|  * OpenRouter           |   +-----------------------+   |  * Flysystem Asset Storage|
|  * Local Ollama Instance|                               |  * MySQL / PostgreSQL   |
+-------------------------+                               +-------------------------+
```

---

## 2. Component Specification

### 2.1 Backend Component (CMS Engine)
* **Technology Stack:** PHP 8.x, Symfony Framework, Doctrine ORM, API Platform (optional for rapid REST/GraphQL scaffolding).
* **Role:** Data persistence, authentication, authorization, and frontend API service.
* **Responsibilities:**
  * Exposes secure endpoints (`/api/v1/...`) using JWT (JSON Web Tokens) or OAuth2.
  * Manages entities: `Article`, `Category`, `Tag`, `MediaAsset`, `User`.
  * Handles image processing, thumbnail generation, and storage (local or S3 compatible via Flysystem).
  * Serves the public blog frontend (built with React/Storybook consuming the Symfony API).

### 2.2 Desktop Client (AI Authoring Workstation)
* **Technology Stack:** C# (.NET 8/9), Avalonia UI (cross-platform desktop UI framework).
* **Role:** Interactive content creation environment.
* **Responsibilities:**
  * Provides a modern GUI running natively on Windows, Linux, and macOS.
  * Stores user API credentials safely using OS-native secure stores (Windows Credential Manager, macOS Keychain, Linux Secret Service).
  * Manages local drafts using an embedded SQLite database (**Offline-First strategy**).
  * Executes AI Agent workflows via **Microsoft Semantic Kernel** or custom tool-calling wrappers.
  * Connects to the Symfony backend API to push finished articles and assets.

---

## 3. The AI Agent Ecosystem & Workflow

The core differentiator of this system is the **AI Agent**, which operates as an autonomous background loop rather than a simple text-completion call.

### 3.1 Agent Tooling Capabilities (Tools / Function Calling)
The C# desktop client provides the LLM agent with executable tools:
1. `WebSearchTool`: Executes queries via Tavily, Exa, or DuckDuckGo to fetch up-to-date documentation, news, or code examples.
2. `CodeValidatorTool`: Optionally validates generated PHP, JavaScript, or C# code blocks for syntax correctness.
3. `ImageGeneratorTool`: Calls image synthesis APIs (DALL-E 3, Flux) using structured prompts derived from the article context.
4. `DraftSaverTool`: Writes structured Markdown output and image paths directly into the local SQLite store.

### 3.2 End-to-End Generation Sequence

```
User Prompts Agent ("Write an article on PHP 8.4 features with code samples & cover image")
  │
  ├─► Step 1: Agent determines knowledge gaps -> Invokes `WebSearchTool`
  │           └─► Fetches RFCs and latest PHP 8.4 release notes.
  │
  ├─► Step 2: Agent synthesizes content -> Generates structured Markdown text.
  │
  ├─► Step 3: Agent designs visual concept -> Invokes `ImageGeneratorTool`
  │           └─► Downloads generated cover image to local temporary disk.
  │
  ├─► Step 4: Agent compiles draft -> Invokes `DraftSaverTool`
  │           └─► Saves post + media paths into local SQLite database.
  │
  └─► Step 5: User reviews draft in Avalonia UI -> Clicks "Publish"
              └─► Desktop App uploads assets and POSTs post JSON to Symfony REST API.
```

---

## 4. Monetization, Cost Control & BYOK Model

To keep platform operating costs at zero for the CMS owner, the desktop application implements a **Bring Your Own Key (BYOK)** model:

* **Flexible API Provider Support:** The user can configure credentials for:
  * Commercial Cloud Models: OpenAI, Anthropic, Groq, OpenRouter.
  * Local/Offline Models: Local Ollama instances (`http://localhost:11434`).
* **Cost Estimation Per Article (Cloud Providers):**
  * Text Generation (e.g., GPT-4o / Claude 3.5 Sonnet): ~$0.005 - $0.02
  * Image Generation (DALL-E 3 / Flux): ~$0.04 - $0.08
  * **Total Estimated Cost:** ~$0.05 / article (paid directly by the user to the provider).

---

## 5. Security & Data Integrity Considerations

1. **Decoupled Security Bounds:** The CMS backend never receives or stores the user's AI provider API keys.
2. **API Authentication:** All communication between the Desktop Client and Symfony Backend uses HTTPS with JWT Bearer token authentication.
3. **Local Encryption:** User API keys saved within the desktop application settings are encrypted at rest using OS-native encryption API abstractions.
4. **Offline-First Safeguard:** Network disruptions during AI generation do not cause data loss. The generation lifecycle is completed locally before network synchronization is attempted.

---

## 6. Summary Matrix

| Metric | Server-Side Agent Architecture | Client-Side Desktop Agent Architecture (Chosen) |
| :--- | :--- | :--- |
| **Server Load** | High (Long-running process workers required) | Low (Pure CRUD API requests) |
| **HTTP Timeouts** | High Risk (Requires Symfony Messenger/Queues) | Zero Risk (Runs asynchronously on client) |
| **Platform Cost** | Operator pays all LLM/Image token costs | Operator pays $0 (User provides API keys) |
| **Cross-Platform** | Web UI only | Windows, Linux, macOS Native App |
| **Offline Capability** | None | Full local draft editing & local LLM support |

---
*Document prepared for project implementation and design verification.*
