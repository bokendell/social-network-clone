import React from 'react';
import { Carousel } from 'flowbite-react';

export default function PostMedia({ post}) {
    // console.log('PostMedia', post);

    const media = [...post.images, ...post.videos];

    media.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
    return (
        <div className="h-56 sm:h-64 xl:h-80 2xl:h-96">
            <Carousel slide={false} >
                {media.map((mediaItem, index) => {
                    const uniqueKey = `${mediaItem.id}-${index}`;
                    if (mediaItem.type === 'image') {
                        return (
                            <img key={uniqueKey} src={mediaItem.url} alt={mediaItem.alt} />
                        );
                    }

                    if (mediaItem.type === 'video') {
                        return (
                            <video key={uniqueKey} src={mediaItem.url} controls />
                        );
                    }
                })}
            </Carousel>
        </div>
    )
}
