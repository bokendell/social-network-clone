import { useState } from 'react';
import axios from 'axios';
import Post from "@/Components/Posts/Post";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { UserListModal } from "@/Components/UserListModal";
import { Avatar } from "@/Components/CatalystComponents/avatar";
import { Button } from "@/Components/CatalystComponents/button";
import { Tab } from "@headlessui/react";
import { Head } from '@inertiajs/react';
import { Dropdown, DropdownButton, DropdownItem, DropdownMenu } from '@/Components/CatalystComponents/dropdown';

export default function Show({ auth, user, followers: initialFollowers, following: initialFollowing, posts, reposts, likes, relatedFriends}) {
    const [followers, setFollowers] = useState(initialFollowers);
    const [following, setFollowing] = useState(initialFollowing);
    const [isFollowing, setIsFollowing] = useState(followers.data.some(entry => entry.requester.id === auth.user.id));
    const [isUserProfile, setIsUserProfile] = useState(auth.user.id == user.id);
    const tabClass = 'ui-selected:text-gray-800 ui-not-selected:text-gray-500 px-4 cursor-pointer flex justify-center items-center flex-col relative';

    
    const deleteUser = () => {
        return;
    }

    const handleFollow = (e) => {
        e.preventDefault();
        try {
            axios.post(`/feed/friends/follow/${user.id}`);
            setIsFollowing(true);
            setFollowers({
                ...followers,
                data: [...followers.data, { requester: auth.user, accepter: user }]
            });
        }
        catch (error) {
            console.error("Error following user:", error);
            if (error.response) {
                console.error("Error status:", error.response.status);
                console.error("Error data:", error.response.data);
                alert(`Error following user: ${error.response.data.message}`);
            } else if (error.request) {
                console.error("No response:", error.request);
                alert("No response from server");
            } else {
                console.error("Error message:", error.message);
                alert("Error sending request");
            }
        }
    }

    const handleUnfollow = (e) => {
        e.preventDefault();
        try {
            axios.delete(`/feed/friends/${user.id}`);
            setIsFollowing(false);
            setFollowers({
                ...followers,
                data: followers.data.filter(entry => entry.requester.id !== auth.user.id)
            });
        }
        catch (error) {
            console.error("Error unfollowing user:", error);
            if (error.response) {
                console.error("Error status:", error.response.status);
                console.error("Error data:", error.response.data);
                alert(`Error unfollowing user: ${error.response.data.message}`);
            } else if (error.request) {
                console.error("No response:", error.request);
                alert("No response from server");
            } else {
                console.error("Error message:", error.message);
                alert("Error sending request");
            }
        }
    }

    const handleBlock = (e) => {
        e.preventDefault();
        try {
            axios.put(`/feed/friends/${user.id}`, { status: 'blocked' });
            setIsFollowing(false);
            setFollowers({
                ...followers,
                data: followers.data.filter(entry => entry.requester.id !== auth.user.id)
            });
        }
        catch (error) {
            console.error("Error blocking user:", error);
            if (error.response) {
                console.error("Error status:", error.response.status);
                console.error("Error data:", error.response.data);
                alert(`Error blocking user: ${error.response.data.message}`);
            } else if (error.request) {
                console.error("No response:", error.request);
                alert("No response from server");
            } else {
                console.error("Error message:", error.message);
                alert("Error sending request");
            }
        }
    }

    const getFollowedBy = () => {
        // if followed by more than 3
        if (relatedFriends.data.length > 3) {
            return `Followed by ${relatedFriends.data[0].requester.name}, ${relatedFriends.data[1].requester.name}, ${relatedFriends.data[2].requester.name}, and ${relatedFriends.data.length - 3} others`;
        // if followed by 3
        } else if (relatedFriends.data.length === 3) {
            return `Followed by ${relatedFriends.data[0].requester.name}, ${relatedFriends.data[1].requester.name}, and ${relatedFriends.data[2].requester.name}`;
        // if followed by 2
        } else if (relatedFriends.data.length === 2) {
            return `Followed by ${relatedFriends.data[0].requester.name} and ${relatedFriends.data[1].requester.name}`;
        // if followed by 1
        } else if (relatedFriends.data.length === 1) {
            return `Followed by ${relatedFriends.data[0].requester.name}`;
        }
        // if followed by 0
        return '';
    }

    const getProfileButton = () => {
        if (isUserProfile) {
            return (
                <Button href="/profile">Edit Profile</Button>
            )
        }
        else if (isFollowing) {
            return (
                <Dropdown>
                    <DropdownButton outline>
                        Following
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-6 h-6">
                            <path strokeLinecap="round" strokeLinejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>

                    </DropdownButton>
                    <DropdownMenu>
                        <DropdownItem onClick={handleUnfollow}>Unfollow</DropdownItem>
                        <DropdownItem onClick={handleBlock}>Block</DropdownItem>
                    </DropdownMenu>
                </Dropdown>
            )
        }
        else {
            return (
                <Button onClick={handleFollow}>
                    Follow
                </Button>
            )
        }
    }

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex">
                    <Avatar className='size-50 mr-3 self-start' initials={user.name.charAt(0)} src={auth.user.profile_pic_url} />
                    <div>
                        <h1 className="font-semibold text-2xl text-gray-800">{user.name}</h1>
                        <div className="text-gray-500">@{user.username}</div>
                        <div>{user.bio}</div>
                        <div className="flex items-center">
                            <span className="mr-2">{posts.posts.length} posts</span>
                            <span className="mr-2"><UserListModal buttonTitle={`${followers.data.length} followers`} userList={followers.data} title="followers" followers></UserListModal></span>
                            <span className="mr-2"><UserListModal buttonTitle={`${following.data.length} following`} userList={following.data} title="following" following></UserListModal></span>
                            {getProfileButton()}
                        </div>
                        {/* {relatedFriends && relatedFriends.data.length > 0 &&
                            <div className="flex items-center">
                                <UserListModal buttonTitle={getFollowedBy()} userList={relatedFriends.data} title="followed by" followers></UserListModal>
                            </div>
                        } */}
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
