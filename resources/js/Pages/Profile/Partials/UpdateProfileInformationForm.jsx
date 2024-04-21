import InputError from '@/Components/InputError';
import { Avatar } from '@/Components/CatalystComponents/avatar'
import { Link, useForm, usePage } from '@inertiajs/react';
import { Transition } from '@headlessui/react';
import { Button } from '@/Components/CatalystComponents/button';
import { Strong, Text } from '@/Components/CatalystComponents/text';
import { Field, FieldGroup, Fieldset, Label, ErrorMessage} from '@/Components/CatalystComponents/fieldset';
import { Input } from '@/Components/CatalystComponents/input';

export default function UpdateProfileInformation({ mustVerifyEmail, status, className = '' }) {
    const user = usePage().props.auth.user;

    const { data, setData, patch, errors, processing, recentlySuccessful } = useForm({
        name: user.name,
        email: user.email,
        bio: user.bio,
        username: user.username,
        profile_pic_url: user.profile_pic_url
    });

    const submit = (e) => {
        e.preventDefault();

        patch(route('profile.update'));
    };

    return (
        <section className={className}>
            <header>
                <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100"><Strong>Profile Information</Strong></h2>

                <Text className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Update your account's profile information and email address.
                </Text>
            </header>

            <form onSubmit={submit} className="mt-6 space-y-6">
                <Fieldset>
                    <FieldGroup>
                        <div className='flex items-end space-x-4'>
                            <Avatar className="size-20 self-center mr-2" initials={data.name.charAt(0)} src={data.profile_pic_url}/>
                            <Field className='flex-1'>
                                <Label htmlFor="url">Profile Picture URL</Label>

                                <Input
                                    id="url"
                                    className="mt-1 block w-full "
                                    value={data.profile_pic_url}
                                    onChange={(e) => setData('profile_pic_url', e.target.value)}
                                    required
                                    autoComplete="name"
                                />
                            </Field>

                            {errors.profile_pic_url && ( <InputError className="mt-2" message={errors.profile_pic_url} />)}
                        </div>

                        <Field>
                            <Label htmlFor="name">Name</Label>

                            <Input
                                id="name"
                                className="mt-1 block w-full"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                required
                                autoComplete="name"
                            />

                            {errors.name && <InputError className="mt-2" message={errors.name} />}
                        </Field>

                        <Field>
                            <Label htmlFor="email">Email</Label>

                            <Input
                                id="email"
                                type="email"
                                className="mt-1 block w-full"
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                                required
                                autoComplete="username"
                            />

                            {errors.email && <InputError className="mt-2" message={errors.email} />}
                        </Field>

                        <Field>
                            <Label htmlFor="username">Username</Label>

                            <Input
                                id="username"
                                className="mt-1 block w-full"
                                value={data.username}
                                onChange={(e) => setData('username', e.target.value)}
                                required
                                autoComplete="username"
                            />

                            {errors.username && <InputError className="mt-2" message={errors.username} />}
                        </Field>

                        <Field>
                            <Label htmlFor="bio">Bio</Label>

                            <Input
                                id="bio"
                                className="mt-1 block w-full"
                                value={data.bio}
                                onChange={(e) => setData('bio', e.target.value)}
                                required
                                autoComplete="bio"
                            />

                            {errors.bio && <InputError className="mt-2" message={errors.bio} />}
                        </Field>

                        {mustVerifyEmail && user.email_verified_at === null && (
                            <div>
                                <Text className="text-sm mt-2 text-gray-800 dark:text-gray-200">
                                    Your email address is unverified.
                                    <Link
                                        href={route('verification.send')}
                                        method="post"
                                        as="button"
                                        className="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                                    >
                                        Click here to re-send the verification email.
                                    </Link>
                                </Text>

                                {status === 'verification-link-sent' && (
                                    <Text className="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                                        A new verification link has been sent to your email address.
                                    </Text>
                                )}
                            </div>
                        )}

                        <div className="flex items-center gap-4">
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
                        </div>
                    </FieldGroup>
                </Fieldset>
            </form>
        </section>
    );
}
