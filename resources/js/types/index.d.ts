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

export interface Secret {
    id: number;
    user_id: number;
    name?: string;
    content: string;
    expired_at: string;
    created_at: string;
    updated_at: string;
}

export interface SecretsForm extends Partial<Secret> {
    name: string;
    content: string;
    expired_at: '5m' | '10m' | '30m' | '1h' | '6h' | '12h' | '1d';
    password?: string;
    password_protect: boolean;
    [key: string]: string | boolean | undefined;
};

export type BreadcrumbItemType = BreadcrumbItem;
