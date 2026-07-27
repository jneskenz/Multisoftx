# MultiSoft Suite - INIT.md

Guia de inicializacion y referencia rapida del proyecto. Este archivo describe el estado real del repositorio y debe mantenerse actualizado cuando cambien el stack, la arquitectura o el roadmap.

## 1. Descripcion del proyecto

**MultiSoft Suite** es una plataforma integrada de gestion empresarial que reune varios sistemas de negocio bajo una misma base de codigo, una misma base de datos y una misma experiencia de usuario.

- **Tipo de proyecto:** No-SaaS, instalacion single-tenant.
- **Patron de arquitectura:** Monolito modular con Core + modulos independientes.
- **Estado actual:** Fase 0 completa parcialmente y Fase 1 iniciada.
- **UI actual:** Vuexy starter kit integrado parcialmente en el dashboard.

## 2. Stack real del repositorio

| Capa | Tecnologia | Version/Estado |
|---|---|---|
| Backend | Laravel | 13.x |
| Lenguaje | PHP | 8.4 |
| Base de datos local | PostgreSQL | Configurado via `.env` |
| Auth | Laravel Fortify | Instalado |
| Frontend reactivo | Livewire | 4.x |
| Componentes UI | Flux UI | 2.x |
| Template dashboard | Vuexy | Assets en `public/vuexy` |
| CSS/build | Vite + Tailwind CSS v4 + Bootstrap/Vuexy assets | Activo |
| Gestion de modulos | nwidart/laravel-modules | Instalado |
| Roles y permisos | spatie/laravel-permission | Instalado, migrado |
| Auditoria | spatie/laravel-activitylog | Instalado, migrado |
| Tests | Pest | 4.x (65 tests pasan, 2 skip, 1 risky) |

Nota: `barryvdh/laravel-dompdf` esta documentado como necesidad futura, pero actualmente no esta instalado.

## 3. Arquitectura

El Core/Shell Laravel gestiona autenticacion, equipos, usuarios, layout general, configuracion y dependencias compartidas. Los sistemas de negocio viven en `Modules/`:

- `Modules/CRM`
- `Modules/ERP`
- `Modules/RRHH`

Los modulos existen y estan activos, pero por ahora son stubs CRUD genericos generados por `nwidart/laravel-modules`. Todavia no implementan los MVP de negocio.

Los tres modulos comparten la misma estructura generada por `nwidart/laravel-modules`:

```
Modules/{Module}/
  app/
    Providers/
    Http/Controllers/
  config/
  database/
  resources/views/
  routes/
  module.json
  composer.json
```

Entidades compartidas previstas:

- `users`: existe.
- `teams`, `team_members`, `team_invitations`: existen.
- `partners`, `products`, `companies`: pendientes.

## 4. Rutas reales actuales

| Ruta | Estado | Descripcion |
|---|---|---|
| `/` | Activa | Welcome/home |
| `/{current_team}/dashboard` | Activa | Dashboard Core protegido por auth, verified y membership |
| `/settings/profile` | Activa | Perfil de usuario |
| `/settings/security` | Activa | Seguridad/2FA/passkeys UI parcial |
| `/settings/appearance` | Activa | Apariencia |
| `/teams` | Activa | Equipos (gestion) |
| `/users` | Activa | Usuarios (gestion) |
| `/roles` | Activa | Roles y permisos (gestion) |
| `/crm` | Activa | Stub CRUD CRM |
| `/erp` | Activa | Stub CRUD ERP |
| `/rrhh` | Activa | Stub CRUD RRHH |
| `/api/v1/crm` | Activa | Stub API CRM |
| `/api/v1/erp` | Activa | Stub API ERP |
| `/api/v1/rrhh` | Activa | Stub API RRHH |
| `/admin` | Pendiente | Administracion Core |

Las rutas de modulos estan normalizadas a singular: `/crm`, `/erp`, `/rrhh`.
Las rutas de gestion (teams/users/roles) fueron movidas a `routes/management.php` y ahora viven fuera de `settings/*`.

## 5. Requisitos locales

- PHP 8.4.
- Extensiones requeridas: `pdo_pgsql`, `pgsql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`.
- Para ejecutar tests actuales con SQLite in-memory tambien se requiere `pdo_sqlite`.
- Composer 2.x.
- Node.js y npm.
- PostgreSQL configurado en `.env`.

## 6. Instalacion local

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build
```

Para desarrollo:

```bash
composer run dev
```

## 7. Estado de modulos

Verificacion:

```bash
php artisan module:list
```

Estado actual esperado:

```text
[Enabled] CRM
[Enabled] ERP
[Enabled] RRHH
```

## 8. Estado de migraciones

Migraciones Core ejecutadas:

- `users`
- `cache`
- `jobs`
- `teams`
- `current_team_id` en `users`

Migraciones 2FA:

- `2026_06_18_220655_add_two_factor_columns_to_users_table.php` (agregada en Fase 0.5)

Migraciones Spatie ejecutadas:

- `2026_06_14_005050_create_permission_tables.php`
- `2026_06_14_005103_create_activity_log_table.php`

## 9. Blockers tecnicos actuales

1. Navbar superior aun mezcla enlaces demo de Vuexy (`pages-*.html`, `auth-login-cover.html`) con rutas reales Laravel.
2. Falta cerrar el dropdown de usuario con acciones reales (perfil/configuracion/logout por rutas del sistema).
3. Breadcrumbs nuevo (`resources/views/components/breadcrumbs.blade.php`) requiere limpieza de comentarios temporales.
4. Vistas de roles/usuarios tienen copys cruzados (ej. textos de usuarios en pantallas de roles) pendientes de ajuste.
5. Verificar si se requiere compatibilidad retroactiva para rutas antiguas `settings/users`, `settings/roles`, `settings/teams`.

## 10. Verificacion actual

Comandos ejecutados recientemente:

```bash
php artisan route:list --except-vendor
php artisan module:list
php artisan migrate:status
npm run build
php artisan test --compact tests/Feature/DashboardTest.php
php artisan view:cache
```

Resultado:

- `route:list`: correcto.
- `module:list`: CRM, ERP y RRHH activos.
- `migrate:status`: todas las migraciones actuales ejecutadas.
- `npm run build`: correcto.
- `php artisan test`: 65 passed, 2 skipped, 1 risky.
- `php artisan view:cache`: correcto.

## 11. Convenciones vigentes

- Namespaces por modulo: `Modules\CRM`, `Modules\ERP`, `Modules\RRHH`.
- Core usa modelos compartidos en `app/Models`.
- Nuevas rutas protegidas deben considerar equipo actual cuando dependan del dashboard.
- El dashboard debe usar Vuexy real, no estilos manuales similares.
- Las pruebas deben escribirse con Pest.
- Si se modifica PHP, ejecutar:

```bash
vendor/bin/pint --dirty --format agent
```

## 12. Roadmap corto actualizado

- [x] Fase 0 - Proyecto Laravel base.
- [x] Fase 0 - Fortify/Auth base.
- [x] Fase 0 - Equipos y membresias.
- [x] Fase 0 - Modulos CRM, ERP y RRHH creados y activos.
- [x] Fase 0 - Ejecutar migraciones de Permission y Activitylog.
- [x] Fase 0 - Resolver componente faltante de passkeys o desactivar UI incompleta.
- [x] Fase 0 - Corregir rutas antiguas del dashboard.
- [x] Fase 0.5 - Habilitar entorno de tests (65 de 67 tests pasando).
- [x] Fase 0.5 - Unificar estructura PSR-4 de modulos (CRM, ERP, RRHH).
- [x] Fase 1 - Consolidar layout maestro Vuexy (settings, equipos y pages internas ahora usan Vuexy).
- [x] Fase 1 - Menu dinamico por modulo (sidebar lee modulos activos via Module::allEnabled()).
- [ ] Fase 1 - Roles y permisos base (en progreso de UI/rutas, faltan ajustes finales de copy y navegacion).
- [ ] Fase 1 - Navbar final productivo (reemplazar enlaces demo por rutas reales y logout funcional).
- [ ] Fase 2 - RRHH MVP.
- [ ] Fase 3 - CRM MVP.
- [ ] Fase 4 - ERP MVP.
- [ ] Fase 5 - Integracion entre modulos via eventos/listeners.
- [ ] Fase 6 - Hardening, tests y despliegue.

## 13. Referencias

- nwidart/laravel-modules: https://github.com/nWidart/laravel-modules
- spatie/laravel-permission: https://github.com/spatie/laravel-permission
- spatie/laravel-activitylog: https://github.com/spatie/laravel-activitylog
- Vuexy: assets locales en `public/vuexy`
