import { Head } from "@inertiajs/react"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout"
import { useState } from 'react'
import { Tab } from '@headlessui/react'


export default function Notifications({ auth, requests}) {
    const tabClass = 'ui-selected:text-gray-800 ui-not-selected:text-gray-500 px-4 cursor-pointer flex justify-center items-center flex-col relative';
    return (
        <AuthenticatedLayout
            user={auth.user}
        >
            <Head title="Notifications" />
            <div>
                <div className="max-w-2xl mx-auto px-4">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-5 p-5">
                        <h1 className="text-lg font-semibold">Notifications</h1>
                        <Head title="Notifications" />
                        <Tab.Group>
                            <div className="flex justify-center w-full bg-white relative">
                                <Tab.List className="flex justify-center w-full">
                                    <Tab className={tabClass}>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-6 h-6 mb-2">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <div className="absolute bottom-0 left-0 right-0 h-1 ui-selected:bg-gray-800 ui-not-selected:bg-transparent"></div>
                                    </Tab>
                                    <Tab className={tabClass}>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-6 h-6 mb-2">
                                            <path strokeLinecap="round" strokeLinejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
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
                                        <div>{requests.length} requests</div>
                                        </div>
                                    </div>
                                </Tab.Panel>
                                <Tab.Panel>
                                    <div>
                                        <div className="max-w-2xl mx-auto px-4">
                                            {/* <Post posts={posts.posts} auth={auth}/> */}
                                        </div>
                                    </div>
                                </Tab.Panel>
                                <Tab.Panel>
                                    <div>
                                        <div className="max-w-2xl mx-auto px-4">
                                            {/* <Post posts={reposts.posts} auth={auth}/> */}
                                        </div>
                                    </div>
                                </Tab.Panel>
                                <Tab.Panel>
                                    <div>
                                        <div className="max-w-2xl mx-auto px-4">
                                            {/* <Post posts={likes.posts} auth={auth}/> */}
                                        </div>
                                    </div>
                                </Tab.Panel>
                            </Tab.Panels>
                        </Tab.Group>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    )
}
