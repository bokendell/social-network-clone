import pluralize from 'pluralize';
import axios from 'axios';
import React, { useState } from 'react';
import { Button } from "flowbite-react";
import CommentsDisclosure from './CommentsDisclosure';
import { Link } from '@inertiajs/react';

export default function PostInteractions({ post: initialPost, auth }) {
    const [post, setPost] = useState(initialPost);
    const [isOpen, setIsOpen] = useState(false);
    const [liked, setLiked] = useState(post.likes.some((like) => like.user.id === auth.user.id));
    const [reposted, setReposted] = useState(post.reposts.some((repost) => repost.user.id === auth.user.id));
    const [respostID, setRepostID] = useState(post.reposts.find((repost) => repost.user.id === auth.user.id)?.id);

    const handleRepost = (e) => {
        e.preventDefault();
        if (reposted) {
            unrepost(e);
        } else {
            repost(e);
        }
    }

    const repost = async (e) => {
        e.preventDefault();
        try {
            const response = await axios.post(`/feed/posts/${post.id}/reposts`);
            setPost({
                ...post,
                reposts: [...post.reposts, response.data]
            })
            setReposted(true);
            setRepostID(response.data.id);
        } catch (error) {
            console.error("Error reposting post:", error);
            if (error.response) {
                console.error("Error status:", error.response.status);
                console.error("Error data:", error.response.data);
                alert(`Error reposting post: ${error.response.data.message}`);
            } else if (error.request) {
                console.error("No response:", error.request);
                alert("No response from server");
            } else {
                console.error("Error message:", error.message);
                alert("Error sending request");
            }
        }
    }

    const unrepost = async (e) => {
        e.preventDefault();
        try {
            const response = await axios.delete(`/feed/posts/${post.id}/reposts/${respostID}`);
            setPost({
                ...post,
                reposts: post.reposts.filter((repost) => repost.user.id !== auth.user.id)
            })
            setReposted(false);
        } catch (error) {
            console.error("Error unreposting post:", error);
            if (error.response) {
                console.error("Error status:", error.response.status);
                console.error("Error data:", error.response.data);
                alert(`Error unreposting post: ${error.response.data.message}`);
            } else if (error.request) {
                console.error("No response:", error.request);
                alert("No response from server");
            } else {
                console.error("Error message:", error.message);
                alert("Error sending request");
            }
        }
    }

    const handleLike = (e) => {
        e.preventDefault();
        if (liked) {
            unlike(e);
        } else {
            like(e);
        }
    }


    const unlike = async (e) => {
        e.preventDefault();
        try {
            const response = await axios.delete(`/feed/posts/${post.id}/likes`);
            setPost({
                ...post,
                likes: post.likes.filter((like) => like.user.id !== auth.user.id)
            })
            setLiked(false);
        } catch (error) {
            console.error("Error unliking post:", error);
            if (error.response) {
                console.error("Error status:", error.response.status);
                console.error("Error data:", error.response.data);
                alert(`Error unliking post: ${error.response.data.message}`);
            } else if (error.request) {
                console.error("No response:", error.request);
                alert("No response from server");
            } else {
                console.error("Error message:", error.message);
                alert("Error sending request");
            }
        }
    }


    const like = async (e) => {
        e.preventDefault();
        try {
            const response = await axios.post(`/feed/posts/${post.id}/likes`);
            setPost({
                ...post,
                likes: [...post.likes, response.data]
            })
            setLiked(true);
        } catch (error) {
            console.error("Error liking post:", error);
            if (error.response) {
                console.error("Error status:", error.response.status);
                console.error("Error data:", error.response.data);
                alert(`Error liking post: ${error.response.data.message}`);
            } else if (error.request) {
                console.error("No response:", error.request);
                alert("No response from server");
            } else {
                console.error("Error message:", error.message);
                alert("Error sending request");
            }
        }
    }

    const toggleComments = () => {
        setIsOpen(!isOpen);
    }
    return (
        <>
            <div className='flex'>
                <Button onClick={handleLike} className={`hover:text-red-600 text-gray-500 ${liked ? 'text-red-500' : ''}`} color="white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-6 h-6">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                    </svg>
                </Button>
                <Button onClick={toggleComments} className="hover:text-black text-gray-500" color="white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-6 h-6">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
                    </svg>
                </Button>
                <Button onClick={handleRepost} className={`hover:text-gray-800 text-gray-500 ${reposted ? 'text-gray-800' : ''}`} color="white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-6 h-6">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 0 0-3.7-3.7 48.678 48.678 0 0 0-7.324 0 4.006 4.006 0 0 0-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 0 0 3.7 3.7 48.656 48.656 0 0 0 7.324 0 4.006 4.006 0 0 0 3.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3-3 3" />
                    </svg>
                </Button>
            </div>
            <div className="flex justify-between items-center">
                <div className="flex mr-4">
                    <div className="mr-2">{post.likes.length.toLocaleString('en-US')} {pluralize("like", post.likes.length)}</div>
                    <div className="mr-2">{post.reposts.length.toLocaleString('en-US')} {pluralize("repost", post.reposts.length)}</div>
                </div>
            </div>
            <div className='flex'>
                <div><strong><Link href={`/profile/${post.user.id}`}>{post.user.username}</Link></strong> {post.content}</div>
            </div>
            <CommentsDisclosure post={post} auth={auth} isOpen={isOpen} />
        </>

    )

}

