import Post from "@/Components/Post";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { FollowListModal } from "@/Components/FollowListModal";
import { Avatar } from "flowbite-react";
import { Head } from '@inertiajs/react';

export default function Show({ auth, user, followers, following, posts}) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex">
                    <Avatar className='mr-3' rounded />
                    <div>
                        <h1 className="font-semibold text-2xl text-gray-800">{user.name}</h1>
                        <div className="text-gray-500">@{user.username}</div>
                        <div className="flex items-center">
                            <span className="mr-2">{posts} posts</span>
                            <span className="mr-2"><FollowListModal title={`${followers.data.length} followers`} followList={followers} followers={true}></FollowListModal> </span>
                            <span className="mr-2"><FollowListModal title={`${following.data.length} following`} followList={following} following={true}></FollowListModal> </span>
                        </div>
                    </div>
                </div>

            }
        >
            <Head title={`@${user.username}`} />

            <div>
                <div className="max-w-2xl mx-auto px-4">
                    <Post userID={user.id}/>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
