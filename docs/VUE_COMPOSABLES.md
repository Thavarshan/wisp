# Vue Composables Documentation

This document describes the Vue.js composables created to abstract business logic from components and improve code reusability.

## Overview

All composables are located in `resources/js/composables/` and can be imported individually or as a barrel export from `resources/js/composables/index.ts`.

## Available Composables

### 1. useSecretTimer

**Purpose**: Manages countdown timers for secret expiration with automatic page refresh.

**Usage**:
```typescript
import { useSecretTimer } from '@/composables/useSecretTimer';

const { countdown, isExpired } = useSecretTimer(props.expired_at, () => {
    window.location.reload();
});
```

**Features**:
- Real-time countdown display (e.g., "5m 30s")
- Automatic callback when expired
- Proper cleanup on component unmount
- Returns readonly reactive values

### 2. useClipboard

**Purpose**: Provides copy-to-clipboard functionality with toast notifications.

**Usage**:
```typescript
import { useClipboard } from '@/composables/useClipboard';

const { copyToClipboard, isSupported } = useClipboard();

// Copy text to clipboard
copyToClipboard('text to copy');
```

**Features**:
- Automatic toast notifications on success/failure
- Browser compatibility detection
- Error handling for unsupported browsers

### 3. useSecretActions

**Purpose**: Handles secret management operations (deletion, navigation).

**Usage**:
```typescript
import { useSecretActions } from '@/composables/useSecretActions';

const { deleteSecret, isDeleted, isLoading } = useSecretActions();

// Delete a secret
await deleteSecret(secretUid);
```

**Features**:
- Loading states
- Error handling with toast notifications
- Navigation helpers
- Reactive state management

### 4. useSecretApi

**Purpose**: Manages API calls for secret creation and retrieval.

**Usage**:
```typescript
import { useSecretApi } from '@/composables/useSecretApi';

const { createSecret, viewSecret, isSubmitting } = useSecretApi();

// Create a new secret
const result = await createSecret({
    secret: 'my secret',
    expiration: '5m',
    password: 'optional',
    single_view: false
});
```

**Features**:
- Form submission handling
- Loading states
- Validation error handling
- Navigation after successful operations

### 5. useFormValidation

**Purpose**: Provides comprehensive form validation with built-in validation rules.

**Usage**:
```typescript
import { useFormValidation, validationRules } from '@/composables/useFormValidation';

const {
    addField,
    validateAll,
    setFieldValue,
    getFieldError,
    isValid
} = useFormValidation();

// Setup validation
onMounted(() => {
    addField('email', '', [
        validationRules.required(),
        validationRules.email()
    ]);
});
```

**Built-in Validation Rules**:
- `required(message?)`
- `minLength(min, message?)`
- `maxLength(max, message?)`
- `email(message?)`
- `url(message?)`
- `numeric(message?)`
- `integer(message?)`
- `min(value, message?)`
- `max(value, message?)`

## Component Refactoring

The following components have been refactored to use these composables:

### Welcome.vue
- Uses `useSecretApi` for secret creation
- Uses `useFormValidation` for form validation
- Cleaner separation of concerns

### Secret.vue
- Uses `useSecretTimer` for countdown management
- Uses `useClipboard` for copy functionality
- Uses `useSecretActions` for secret deletion
- Reduced complexity and improved readability

### Share.vue
- Uses `useClipboard` for link copying
- Uses `useSecretActions` for secret deletion
- Simplified component logic

## Benefits

1. **Code Reusability**: Logic can be shared across multiple components
2. **Separation of Concerns**: Business logic separated from UI components
3. **Testability**: Composables can be unit tested independently
4. **Maintainability**: Centralized logic reduces duplication
5. **Type Safety**: Full TypeScript support with proper typing
6. **Reactive**: Built on Vue 3 Composition API with reactive state

## Best Practices

1. Import composables at the component level
2. Use destructuring to get only needed functionality
3. Handle loading states appropriately in UI
4. Provide user feedback through toast notifications
5. Clean up resources in `onUnmounted` when needed

## Testing

All composables maintain existing functionality verified by:
- 78 passing unit and feature tests
- Successful production build
- No breaking changes to existing API contracts
