import Post from "@/Components/Post";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { FollowListModal } from "@/Components/FollowListModal";
import { Avatar } from "flowbite-react";
import { Tab } from "@headlessui/react";
import { Head } from '@inertiajs/react';

export default function Show({ auth, user, followers, following, posts, reposts, likes}) {

    const tabClass = 'ui-selected:text-gray-800 ui-not-selected:text-gray-500 px-4 cursor-pointer flex justify-center items-center flex-col relative';
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex">
                    <Avatar className='mr-3 self-start' rounded />
                    <div>
                        <h1 className="font-semibold text-2xl text-gray-800">{user.name}</h1>
                        <div className="text-gray-500">@{user.username}</div>
                        <div className="flex items-center">
                            <span className="mr-2">{posts.posts.length} posts</span>
                            <span className="mr-2"><FollowListModal title={`${followers.data.length} followers`} followList={followers} followers></FollowListModal> </span>
                            <span className="mr-2"><FollowListModal title={`${following.data.length} following`} followList={following} following></FollowListModal> </span>
                        </div>
                    </div>
                </div>

            }
        >
            <Head title={`${user.name} (@${user.username})`} />
            <Tab.Group>
                <div className="flex justify-center w-full bg-white relative">
                    <Tab.List className="flex justify-center w-full">
                        <Tab className={tabClass}>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-6 h-6 mb-2">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M16.5 8.25V6a2.25 2.25 0 0 0-2.25-2.25H6A2.25 2.25 0 0 0 3.75 6v8.25A2.25 2.25 0 0 0 6 16.5h2.25m8.25-8.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-7.5A2.25 2.25 0 0 1 8.25 18v-1.5m8.25-8.25h-6a2.25 2.25 0 0 0-2.25 2.25v6" />
                            </svg>
                            <div className="absolute bottom-0 left-0 right-0 h-1 ui-selected:bg-gray-800 ui-not-selected:bg-transparent"></div>
                        </Tab>
                        <Tab className={tabClass}>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-6 h-6 mb-2">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 0 0-3.7-3.7 48.678 48.678 0 0 0-7.324 0 4.006 4.006 0 0 0-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 0 0 3.7 3.7 48.656 48.656 0 0 0 7.324 0 4.006 4.006 0 0 0 3.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3-3 3" />
                            </svg>
                            <div className="absolute bottom-0 left-0 right-0 h-1 ui-selected:bg-gray-800 ui-not-selected:bg-transparent"></div>
                        </Tab>
                        <Tab className={tabClass}>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-6 h-6 mb-2">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                            </svg>
                            <div className="absolute bottom-0 left-0 right-0 h-1 ui-selected:bg-gray-800 ui-not-selected:bg-transparent"></div>
                        </Tab>
                    </Tab.List>
                    <div className="w-full h-0.5 bg-gray-300 absolute bottom-0 left-0"></div>
                </div>
                <Tab.Panels>
                    <Tab.Panel>
                        <div>
                            <div className="max-w-2xl mx-auto px-4">
                                <Post posts={posts.posts} auth={auth}/>
                            </div>
                        </div>
                    </Tab.Panel>
                    <Tab.Panel>
                        <div>
                            <div className="max-w-2xl mx-auto px-4">
                                <Post posts={reposts.posts} auth={auth}/>
                            </div>
                        </div>
                    </Tab.Panel>
                    <Tab.Panel>
                        <div>
                            <div className="max-w-2xl mx-auto px-4">
                                <Post posts={likes.posts} auth={auth}/>
                            </div>
                        </div>
                    </Tab.Panel>
                </Tab.Panels>
            </Tab.Group>
        </AuthenticatedLayout>
    );
}
