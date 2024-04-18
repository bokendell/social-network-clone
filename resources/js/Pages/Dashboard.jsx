import React, { useEffect, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import PostMedia from '@/Components/PostMedia';
import axios from 'axios';
import PostInteractions from '@/Components/PostInteractions';
import PostHeader from '@/Components/PostHeader';

export default function Dashboard({ auth }) {
    const [posts, setPosts] = useState([]);

    useEffect(() => {
        getPosts();
    }, []);

    const getPosts = () => {
        axios.get(route('feed.posts'))
            .then(response => {
                setPosts(response.data.posts);
            })
            .catch(error => {
                console.error(error);
            });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
        >
            <Head title="Home" />

            <div>
                <div className="max-w-2xl mx-auto px-4">
                    <div>
                        {posts.map(post => (
                            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-5 p-5" key={post.id}>
                                <PostHeader post={post}/>
                                <PostMedia post={post} />
                                <PostInteractions post={post} />
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
