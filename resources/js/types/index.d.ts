import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string; };
    auth: Auth;
    ziggy: Config & { location: string; };
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface SecretsForm {
    name: string;
    content: string;
    expired_at: '5 mins' | '10 mins' | '30 mins' | '1 hour' | '6 hours' | '12 hours' | '1 day';
    password?: string;
    password_protect: boolean;
    [key: string]: string | boolean | undefined;
};

export type BreadcrumbItemType = BreadcrumbItem;
