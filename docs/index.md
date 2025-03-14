# Overview

The Identity and Access Management (IAM) API enables you to programmatically manage user authentication, authorization, and access control within your applications. Built on Laravel and Laravel Passport, our API provides enterprise-grade identity management capabilities through a RESTful interface.

## Current Version

The current version is v1. Access the API at:

```
https://api.cerberus.com/v1
```

## Authentication

All API requests require authentication using OAuth 2.0 Bearer tokens. Include your token in the Authorization header:

```
Authorization: Bearer your-access-token
```

Learn more about authentication in our [Authentication Guide](/authentication).

## Rate Limiting

The API implements rate limiting to ensure stability:

- 60 requests per minute per client
- Rate limit details included in response headers
- See our [Rate Limiting Guide](/rate-limits) for more information

## Core Resources

Our API provides endpoints for managing these core resources:

- **Users** - Create and manage user accounts
- **Roles** - Define and assign user roles
- **Permissions** - Configure granular access controls
- **OAuth Clients** - Manage API client applications
- **Access Tokens** - Handle authentication tokens

Detailed documentation for each resource is available in their respective sections.

## Media Types

The API accepts request bodies encoded as JSON and returns JSON-encoded responses:

```
Content-Type: application/json
Accept: application/json
```

## Common Conventions

- All timestamps are returned in ISO 8601 format: `YYYY-MM-DDTHH:MM:SSZ`
- List endpoints support pagination using `page` and `per_page` parameters
- Search endpoints accept a `q` parameter for filtering results
- Resource IDs are returned as integers

## API Changelog

| Date | Changes |
|------|---------|
| 2024-01-15 | Initial v1 API release |

## Client Libraries

Official client libraries:

- [PHP SDK](/sdks/php)
- [JavaScript SDK](/sdks/javascript)

## Quick Start

Get started quickly with our [5-minute quickstart guide](/getting-started) or explore our [code examples](/examples).

## Support

- [API Status Page](https://status.your-domain.com)
- [Developer Support Portal](https://support.your-domain.com)
- Email: <api-support@your-domain.com>

For more detailed information about specific endpoints and features, please refer to the relevant sections in our documentation.
