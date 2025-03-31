# React Quickstart ⚡

A minimal and customizable React starter template to kickstart your projects with best practices.

## Features

- Vite - Fast build and development environment
- React - Modern UI library for building interactive user interfaces
- TypeScript - Stringly typed for better development experience
- Tailwind CSS - Utility-first CSS framework
- Eslint & Prettier - Code quality and formatting enforcement
- React Router - Client-side routing made easy

## Getting started

### Prerequisites

- [Node JS](https://nodejs.org/)
- [pnpm](https://pnpm.io/)

### Installation

#### Clone the repository

```sh
git clone https://github.com/martin-dinahet/react-quickstart.git
cd react-quickstart
```

#### Install dependencies

```sh
pnpm install
```

#### Development

Start the development server

```sh
pnpm dev
```

Then, open [localhost:5173](http://localhost:5173) in your browser.

#### Build for production

```sh
pnpm build
```

#### Linting & Formatting

Run ESLint to check linting errors:

```sh
pnpm lint
```

Format code with Prettier:

```sh
pnpm format
```

## Directory structure

```
🗁 .
├── 🗋 eslint.config.js
├── 🗋 index.html
├── 🗋 package.json
├── 🗋 pnpm-lock.yaml
├── 🗋 readme.md
├── 🗋 tsconfig.app.json
├── 🗋 tsconfig.json
├── 🗋 tsconfig.node.json
├── 🗋 vite.config.ts
├── 🗁 public
│   └── 🗋 favicon.svg
└── 🗁 src
    ├── 🗋 index.css
    ├── 🗋 main.tsx
    ├── 🗋 vite-env.d.ts
    ├── 🗁 components
    │   └── 🗋 example-component.tsx
    ├── 🗁 layouts
    │   └── 🗋 main-layout.tsx
    ├── 🗁 lib
    │   └── 🗋 utils.ts
    └── 🗁 pages
        └── 🗋 index.tsx
```
