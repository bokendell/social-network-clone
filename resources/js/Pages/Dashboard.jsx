import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import Post from '@/Components/Post';

export default function Dashboard({ auth }) {
    return (
        <AuthenticatedLayout
            user={auth.user}
        >
            <Head title="Home" />

            <div>
                <div className="max-w-2xl mx-auto px-4">
                    <Post />
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
