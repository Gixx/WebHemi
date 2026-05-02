# WebHemi

![WebHemi](./docs/webhemi-logo.png "WebHemi")

An old project on new foundation. Rewrite the blog engine based on Symfony.

[![Minimum PHP Version](https://img.shields.io/badge/PHP->%3D8.4-blue.svg)](https://php.net/)
[![Email](https://img.shields.io/badge/email-navig80@gmail.com-blue.svg?style=flat-square)](mailto:navig80@gmail.com)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

[![WebHemi Development](https://img.shields.io/badge/Development-dev--master-orange.svg)](https://github.com/Gixx/WebHemi)
![Build Status](https://github.com/Gixx/WebHemi/actions/workflows/ci.yml/badge.svg)
[![PHPCS](https://github.com/Gixx/WebHemi/actions/workflows/badge-phpcs.yml/badge.svg)](https://github.com/Gixx/WebHemi/actions/workflows/badge-phpcs.yml)
[![PHPStan](https://github.com/Gixx/WebHemi/actions/workflows/badge-phpstan.yml/badge.svg)](https://github.com/Gixx/WebHemi/actions/workflows/badge-phpstan.yml)
[![Deptrac](https://github.com/Gixx/WebHemi/actions/workflows/badge-deptrac.yml/badge.svg)](https://github.com/Gixx/WebHemi/actions/workflows/badge-deptrac.yml)
[![PHPUnit](https://github.com/Gixx/WebHemi/actions/workflows/badge-phpunit.yml/badge.svg)](https://github.com/Gixx/WebHemi/actions/workflows/badge-phpunit.yml)
[![codecov](https://codecov.io/gh/Gixx/WebHemi/branch/main/graph/badge.svg)](https://codecov.io/gh/Gixx/WebHemi)

## Git hooks

Enable the repository-managed Git hooks (includes a `pre-commit` hook that runs `composer run qa`):

```bash
chmod +x .githooks/pre-commit
git config core.hooksPath .githooks
```
