<x-admin.layout title="My account" active="account">
    <form method="POST" action="{{ route('admin.account.update') }}" class="a-card a-narrow">
        @csrf
        @method('PUT')
        <header class="a-card__head">
            <h2>Login details</h2>
            <p class="a-muted">Enter your current password to save any change.</p>
        </header>
        <div class="a-grid">
            @foreach ([
                ['name', 'Name', 'text', 'name'],
                ['email', 'Email', 'email', 'username'],
                ['password', 'New password (leave empty to keep)', 'password', 'new-password'],
                ['password_confirmation', 'Confirm new password', 'password', 'new-password'],
                ['current_password', 'Current password', 'password', 'current-password'],
            ] as [$name, $label, $type, $autocomplete])
                <div @class(['a-field', 'a-field--full' => $name === 'current_password', 'has-error' => $errors->has($name)])>
                    <label class="a-label" for="{{ $name }}">{{ $label }}</label>
                    <input class="a-input" id="{{ $name }}" type="{{ $type }}" name="{{ $name }}" autocomplete="{{ $autocomplete }}"
                        @if ($type !== 'password') value="{{ old($name, $user->{$name}) }}" @endif
                        @required(in_array($name, ['name', 'email', 'current_password'], true))>
                    @error($name)
                        <p class="a-error">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach
        </div>
        <div class="a-card__foot">
            <button class="a-btn a-btn--primary" type="submit"><x-icon name="check" /> Save</button>
        </div>
    </form>
</x-admin.layout>
