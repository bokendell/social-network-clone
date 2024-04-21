import { useRef, useState } from 'react';
import DangerButton from '@/Components/DangerButton';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import Modal from '@/Components/Modal';
import SecondaryButton from '@/Components/SecondaryButton';
import TextInput from '@/Components/TextInput';
import { useForm } from '@inertiajs/react';
import { Button } from '@/Components/CatalystComponents/button';
import { Strong, Text } from '@/Components/CatalystComponents/text';
import { Field, FieldGroup, Fieldset, Label, ErrorMessage} from '@/Components/CatalystComponents/fieldset';
import { Input } from '@/Components/CatalystComponents/input';

export default function DeleteUserForm({ className = '' }) {
    const [confirmingUserDeletion, setConfirmingUserDeletion] = useState(false);
    const passwordInput = useRef();

    const {
        data,
        setData,
        delete: destroy,
        processing,
        reset,
        errors,
    } = useForm({
        password: '',
    });

    const confirmUserDeletion = () => {
        setConfirmingUserDeletion(true);
    };

    const deleteUser = (e) => {
        e.preventDefault();

        destroy(route('profile.destroy'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: () => passwordInput.current.focus(),
            onFinish: () => reset(),
        });
    };

    const closeModal = () => {
        setConfirmingUserDeletion(false);

        reset();
    };

    return (
        <section className={`space-y-6 ${className}`}>
            <header>
                <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100"><Strong>Delete Account</Strong></h2>

                <Text className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Once your account is deleted, all of its resources and data will be permanently deleted. Before
                    deleting your account, please download any data or information that you wish to retain.
                </Text>
            </header>

            <Button color="red" onClick={confirmUserDeletion}>Delete Account</Button>

            <Modal show={confirmingUserDeletion} onClose={closeModal}>
                <form onSubmit={deleteUser} className="p-6">
                    <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                        <Strong>Are you sure you want to delete your account?</Strong>
                    </h2>

                    <Text className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Once your account is deleted, all of its resources and data will be permanently deleted. Please
                        enter your password to confirm you would like to permanently delete your account.
                    </Text>
                    <Fieldset>
                        <FieldGroup>
                            <Field>
                                <Label className="sr-only" htmlFor="password">Password</Label>

                                <Input
                                    id="password"
                                    type="password"
                                    name="password"
                                    ref={passwordInput}
                                    value={data.password}
                                    onChange={(e) => setData('password', e.target.value)}
                                    className="mt-1 block w-3/4"
                                    placeholder="Password"
                                />

                                {errors.password && <ErrorMessage>{errors.password}</ErrorMessage>}
                            </Field>
                            <Field className="mt-6 flex justify-end">
                                <Button color="zinc" onClick={closeModal}>Cancel</Button>

                                <Button color="red" className="ms-3" disabled={processing}>
                                    Delete Account
                                </Button>
                            </Field>

                        </FieldGroup>
                    </Fieldset>


                </form>
            </Modal>
        </section>
    );
}
