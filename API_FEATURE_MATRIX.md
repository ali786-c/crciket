# API feature matrix

The API is versioned under `/api/v1` and reuses the same Laravel domain services as the web application. Every write operation remains server-authoritative and is protected by Sanctum authentication, Spatie permissions, tournament/team scoping, throttling, validation, and audit logging.

| Feature domain | API resources/actions | Public | Player | Captain | Scorer | Admin | Super Admin |
|---|---|---:|---:|---:|---:|---:|---:|
| Authentication | login, me, logout, session revoke |  | ✓ | ✓ | ✓ | ✓ | ✓ |
| Profile | view/update own profile |  | ✓ | ✓ | ✓ | ✓ | ✓ |
| Tournaments | list/show public tournaments | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Tournament administration | create, update, lifecycle, rules |  |  |  |  | ✓ | ✓ |
| Teams | public team summary; admin CRUD; captain assignment | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Players | public redacted pool; own registration; admin approval/import | ✓ | ✓ |  |  | ✓ | ✓ |
| Draft | public state; captain state/pick; admin control | ✓ |  | ✓ |  | ✓ | ✓ |
| Fixtures | public schedule; admin CRUD/status/match handoff | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Matches | public match center; admin match setup/lifecycle | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Playing XI/toss | captain submission; admin approval/toss |  |  | ✓ |  | ✓ | ✓ |
| Scoring | public state; scorer delivery actions; admin correction | ✓ |  |  | ✓ | ✓ | ✓ |
| Results/standings | public approved results; admin submit/approve | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Reports | role-aware reports and downloads | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| Governance | API clients, tokens, health, audit logs |  |  |  |  |  | ✓ |

## Resource conventions

All successful responses use a `data` envelope. Collection endpoints return `data` arrays and pagination metadata where pagination is enabled. Validation errors use Laravel’s standard `422` JSON response. Authentication failures return `401`; permission failures return `403`; hidden/private resources return `404` to avoid data leakage.

Write endpoints accept an optional `expected_revision` for concurrent stateful resources such as drafts and matches. Clients must treat the returned `revision` as the new synchronization checkpoint.
