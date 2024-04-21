import { useRef } from 'react';
import { Button } from '@/Components/CatalystComponents/button';
import { Strong, Text } from '@/Components/CatalystComponents/text';
import { Field, FieldGroup, Fieldset, Label, ErrorMessage} from '@/Components/CatalystComponents/fieldset';
import { Input } from '@/Components/CatalystComponents/input';
import { useForm } from '@inertiajs/react';
import { Transition } from '@headlessui/react';

export default function UpdatePasswordForm({ className = '' }) {
    const passwordInput = useRef();
    const currentPasswordInput = useRef();

    const { data, setData, errors, put, reset, processing, recentlySuccessful } = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const updatePassword = (e) => {
        e.preventDefault();

        put(route('password.update'), {
            preserveScroll: true,
            onSuccess: () => reset(),
            onError: (errors) => {
                if (errors.password) {
                    reset('password', 'password_confirmation');
                    passwordInput.current.focus();
                }

                if (errors.current_password) {
                    reset('current_password');
                    currentPasswordInput.current.focus();
                }
            },
        });
    };

    return (
        <section className={className}>
            <header>
                <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100"><Strong>Update Password</Strong></h2>

                <Text className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Ensure your account is using a long, random password to stay secure.
                </Text>
            </header>

            <form onSubmit={updatePassword} className="mt-6 space-y-6">
                <Fieldset>
                    <FieldGroup>
                        <Field>
                            <Label htmlFor="current_password">Current Password</Label>

                            <Input
                                id="current_password"
                                ref={currentPasswordInput}
                                value={data.current_password}
                                onChange={(e) => setData('current_password', e.target.value)}
                                type="password"
                                className="mt-1 block w-full"
                                autoComplete="current-password"
                            />

                            {errors.current_password && <ErrorMessage>{errors.current_password}</ErrorMessage>}
                        </Field>

                        <Field>
                            <Label htmlFor="password">New Password</Label>

                            <Input
                                id="password"
                                ref={passwordInput}
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                                type="password"
                                className="mt-1 block w-full"
                                autoComplete="new-password"
                            />

                            {errors.password && <ErrorMessage>{errors.password}</ErrorMessage>}
                        </Field>

                        <Field>
                            <Label htmlFor="password_confirmation">Confirm Password</Label>

                            <Input
                                id="password_confirmation"
                                value={data.password_confirmation}
                                onChange={(e) => setData('password_confirmation', e.target.value)}
                                type="password"
                                className="mt-1 block w-full"
                                autoComplete="new-password"
                            />

                            {errors.password_confirmation && <ErrorMessage>{errors.password_confirmation}</ErrorMessage>}
                        </Field>

                        <Field className="flex items-center gap-4">
                            <Button disabled={processing}>Save</Button>

                            <Transition
                                show={recentlySuccessful}
                                enter="transition ease-in-out"
                                enterFrom="opacity-0"
                                leave="transition ease-in-out"
                                leaveTo="opacity-0"
                            >
                                <Text className="text-sm text-gray-600 dark:text-gray-400">Saved.</Text>
                            </Transition>
                        </Field>
                    </FieldGroup>
                </Fieldset>
            </form>
        </section>
    );
}
