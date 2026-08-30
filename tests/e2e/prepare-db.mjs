import { existsSync, rmSync, writeFileSync } from 'node:fs';

const databasePath = 'storage/wisp-e2e.sqlite';

if (existsSync(databasePath)) {
    rmSync(databasePath);
}

writeFileSync(databasePath, '');
