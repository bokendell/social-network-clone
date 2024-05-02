import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import Post from '@/Components/Posts/Post';

export default function Dashboard({ auth, posts }) {
    (posts.posts);
    return (
        <AuthenticatedLayout
            user={auth.user}
        >
            <Head title="Home" />

            <div>
                <div className="mx-auto max-w-xl px-4 sm:px-6 lg:px-8">
                    <Post posts={posts.posts} auth={auth} />
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
