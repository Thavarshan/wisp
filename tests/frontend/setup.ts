import { vi } from 'vitest';

class ResizeObserverStub {
    observe() {}

    unobserve() {}

    disconnect() {}
}

vi.stubGlobal('ResizeObserver', ResizeObserverStub);
vi.stubGlobal('scrollTo', vi.fn());
