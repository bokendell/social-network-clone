import React, { useEffect, useState, useMemo } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import PostMediaDragDrop from '@/Components/Posts/PostMediaDragDrop';
import { Head, Link, useForm } from '@inertiajs/react';
import { Button } from '@/Components/CatalystComponents/button';
import { Textarea } from '@/Components/CatalystComponents/textarea';
import { Strong, Text } from '@/Components/CatalystComponents/text';
import { Input } from '@/Components/CatalystComponents/input';
import { Field, FieldGroup, Fieldset, Label, Legend, ErrorMessage } from '@/Components/CatalystComponents/fieldset';
import Post from '@/Components/Posts/Post';
import { set } from 'date-fns';

export default function CreatePost({ auth }) {
    const [imageInput, setImageInput] = useState('');
    const [videoInput, setVideoInput] = useState('');
    const [postSuccess, setPostSuccess] = useState(false);
    const [media, setMedia] = useState([]);
    const { data, setData, post, processing, errors, reset } = useForm({
        content: '',
        image_urls: [],
        video_urls: [],
    });

    const submit = (e) => {
        e.preventDefault();
        setData('image_urls', media.filter((mediaItem) => mediaItem.type === 'image').map((mediaItem) => mediaItem.url));
        setData('video_urls', media.filter((mediaItem) => mediaItem.type === 'video').map((mediaItem) => mediaItem.url));
        post(route('feed.posts.create'), {
            onSuccess: () => {
                setPostSuccess(true);
                reset();
                setImageInput('');
                setVideoInput('');
            },
        });
    };

    const handleMediaUpdate = (newMedia) => {
        // console.log(media);
        // console.log(newMedia);
        setMedia(newMedia);
    };

    const createMediaArray = (mediaURLs, type) => {
        const now = new Date();
        return mediaURLs.map((mediaURL, index) => {
            return {
                id: index,
                url: mediaURL,
                type: type,
                created_at: now.toISOString(),
                updated_at: now.toISOString(),
                post: 1,
                user: auth.user,
            };
        });
    };

    const posts = useMemo(() => {
        const now = new Date().toISOString();
        return [{
            id: 1,
            content: data.content,
            created_at: now,
            updated_at: now,
            comments: [],
            likes: [],
            reposts: [],
            images: createMediaArray(media.filter((mediaItem) => mediaItem.type === 'image').map((mediaItem) => mediaItem.url), 'image'),
            videos: createMediaArray(media.filter((mediaItem) => mediaItem.type === 'video').map((mediaItem) => mediaItem.url), 'video'),
            user: auth.user,
        }];
    }, [media]);

    const addImageUrl = () => {
        if (validateUrl(imageInput)) {
            setMedia([...media, { id: media.length, url: imageInput, type: 'image' }]);
            setImageInput('');
        }
    };


    const addVideoUrl = () => {
        if (validateUrl(videoInput)) {
            setMedia([...media, { id: media.length, url: videoInput, type: 'video' }]);
            setVideoInput('');
        }
    };

    const validateUrl = (url) => {
        // Regular expression for validating standard URL
        const urlPattern = new RegExp('^(https?:\\/\\/)?' + // protocol
            '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // domain name
            '((\\d{1,3}\\.){3}\\d{1,3}))' + // or IP (v4) address
            '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
            '(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
            '(\\#[-a-z\\d_]*)?$', 'i'); // fragment locator

        // Regular expression for validating base64 Data URI
        const base64Pattern = new RegExp('^data:image\\/(jpeg|png|gif|bmp);base64,([a-zA-Z0-9+/]+={0,2})$', 'i');

        return urlPattern.test(url) || base64Pattern.test(url);
    };

    const isInvalid = (url) => {
        if (url.length === 0) {
            return false;
        }
        if (validateUrl(url)) {
            return false;
        }
        return true;
    };

    const handleContentChange = (e) => setData('content', e.target.value);
    const handleImageInputChange = (e) => setImageInput(e.target.value);
    const handleVideoInputChange = (e) => setVideoInput(e.target.value);

    return (
        <AuthenticatedLayout
            user={auth.user}
        >
            <Head title="Create Post" />
            <div className="py-6">
                <div className="mx-auto max-w-xl px-4 sm:px-6 lg:px-8 space-y-6">
                    <div className="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <h2 className="text-xl mb-4 font-medium text-gray-900 dark:text-gray-100"><Strong>Create Post</Strong></h2>
                        <Text><Strong>Preview</Strong></Text>
                        <Post disabled posts={posts} auth={auth} />
                    </div>
                    <div className="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                        <form onSubmit={submit}>
                            <Fieldset>
                                <FieldGroup>
                                    <Field>
                                        <Label htmlFor="content">Content</Label>
                                        {data.content.length > 255 && <ErrorMessage>Post content cannot exceed 255 characters</ErrorMessage>}
                                        <Textarea
                                            id="content"
                                            name="content"
                                            value={data.content}
                                            className="mt-1 block w-full"
                                            autoComplete="off"
                                            onChange={handleContentChange}
                                            disabled={processing}
                                            invalid={data.content.length > 255}
                                            required
                                        />
                                        {errors.content ? <ErrorMessage>{errors.content}</ErrorMessage> : null}
                                    </Field>
                                    <Field className=''>
                                        <Label>Media Order</Label>
                                        <PostMediaDragDrop media={media} onMediaUpdate={handleMediaUpdate}/>
                                    </Field>
                                    <div className='flex space-x-2'>
                                        <Field className='flex-1'>
                                            <Label htmlFor="image_urls">Image URLs</Label>
                                            {/* {data.image_urls.map((url, index) => (
                                                <div key={index} className='flex space-x-1'>
                                                    <Strong>{index+1}</Strong>
                                                    <Text key={index}> {url}</Text>
                                                </div>
                                            ))} */}
                                            {isInvalid(imageInput) && <ErrorMessage>Invalid URL</ErrorMessage>}
                                            <Input
                                                id="image_urls"
                                                name="image_urls"
                                                value={imageInput}
                                                className="mt-1 block w-full"
                                                autoComplete="off"
                                                onChange={handleImageInputChange}
                                                invalid={isInvalid(imageInput)}
                                                disabled={data.image_urls.length + data.video_urls.length >= 10 || processing}
                                            />
                                        </Field>
                                        <Field className="self-end">
                                            <Button
                                                onClick={addImageUrl}
                                                disabled={data.image_urls.length + data.video_urls.length >= 10 || processing || isInvalid(imageInput)}
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" className="w-4 h-4">
                                                    <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
                                                </svg>

                                                Add</Button>
                                        </Field>
                                    </div>
                                    {errors.image_urls ? <Field><ErrorMessage>{errors.image_urls}</ErrorMessage></Field> : null}
                                    <div className='flex space-x-2'>
                                        <Field className='flex-1'>
                                            <Label htmlFor="video_urls">Video URLs</Label>
                                            {/* {data.video_urls.map((url, index) => (
                                                <div key={index} className='flex space-x-1'>
                                                    <Strong>{index+1}</Strong>
                                                    <Text key={index}> {url}</Text>
                                                </div>
                                            ))} */}
                                            {isInvalid(videoInput) && <ErrorMessage>Invalid URL</ErrorMessage>}
                                            <Input
                                                id="video_urls"
                                                name="video_urls"
                                                value={videoInput}
                                                className="mt-1 block w-full"
                                                autoComplete="off"
                                                onChange={handleVideoInputChange}
                                                invalid={isInvalid(videoInput)}
                                                disabled={data.image_urls.length + data.video_urls.length >= 10 || processing}
                                            />
                                        </Field>
                                        <Field className="self-end">
                                            <Button
                                                onClick={addVideoUrl}
                                                disabled={data.image_urls.length + data.video_urls.length >= 10 || processing || isInvalid(videoInput)}
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" className="w-4 h-4">
                                                    <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
                                                </svg>

                                                Add</Button>
                                        </Field>
                                    </div>
                                    {errors.video_urls ? <Field><ErrorMessage>{errors.video_urls}</ErrorMessage></Field> : null}
                                    <Field>
                                        <div className="flex items-center justify-end mt-4">
                                            {postSuccess &&
                                                <>
                                                    <Text className="text-green-500">Post created successfully</Text>
                                                    <Link href={`/profile/${auth.user.id}`} className="ml-4 text-sm text-gray-700 underline">View Post</Link>
                                                </>
                                            }

                                            <Button
                                                type="submit"
                                                disabled={data.content.length > 255 ||
                                                        data.image_urls.length + data.video_urls.length >= 10 ||
                                                        isInvalid(imageInput) ||
                                                        isInvalid(videoInput) ||
                                                        data.content.length > 255 ||
                                                        processing
                                                    }
                                            >
                                                Post</Button>
                                        </div>
                                    </Field>
                                </FieldGroup>
                            </Fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
