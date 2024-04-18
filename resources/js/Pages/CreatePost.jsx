import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function CreatePost({ auth}) {
    return (
        <AuthenticatedLayout
            user={auth.user}
        >
            <Head title="Create Post" />

            <div className="max-w-2xl mx-auto px-4">
                <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-5 p-5">
                    <form method="post" action={route('feed.posts.create')}>
                        <div>
                            <label htmlFor="body" className="block text-sm font-medium text-gray-700">Body</label>
                            <div className="mt-1">
                                <textarea
                                    id="body"
                                    name="body"
                                    rows={3}
                                    className="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    defaultValue={''}
                                />
                            </div>
                        </div>

                        <div className="mt-4">
                            <button
                                type="submit"
                                className="inline-flex items-center px-4 py-2 border border-transparent text-base leading-6 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition ease-in-out duration-150"
                            >
                                Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
