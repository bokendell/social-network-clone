import React, { useState } from 'react';
import { Carousel, Modal } from 'flowbite-react';

export default function PostMedia({ post }) {
    const [showModal, setShowModal] = useState(false);
    const [selectedMedia, setSelectedMedia] = useState(null);

    const media = [...post.images, ...post.videos];
    media.sort((a, b) => new Date(a.updated_at) - new Date(b.updated_at));

    const handleMediaClick = (mediaItem) => {
        setSelectedMedia(mediaItem);
        setShowModal(true);
    };

    return (
        <div className="h-56 sm:h-64 xl:h-80 2xl:h-96">
            <Carousel slide={false}>
                {media.map((mediaItem, index) => {
                    const uniqueKey = `${mediaItem.id}-${index}`;
                    if (mediaItem.type === 'image') {
                        return (
                            <img key={uniqueKey} src={mediaItem.url} alt={mediaItem.alt} className="h-full w-full object-cover cursor-pointer" onClick={() => handleMediaClick(mediaItem)} />
                        );
                    }

                    if (mediaItem.type === 'video') {
                        return (
                            <video key={uniqueKey} src={mediaItem.url} controls className="h-full w-full object-cover" />
                        );
                    }
                })}
            </Carousel>

            {showModal && selectedMedia && (
                <Modal show={showModal} onClose={() => setShowModal(false)}>
                    <Modal.Header>
                        {selectedMedia.type === 'video' ? 'Video Preview' : 'Image Preview'}
                    </Modal.Header>
                    <Modal.Body>
                        {selectedMedia.type === 'image' && (
                            <img src={selectedMedia.url} alt={selectedMedia.alt} className="w-full object-contain" />
                        )}
                        {selectedMedia.type === 'video' && (
                            <video src={selectedMedia.url} controls className="w-full object-contain" />
                        )}
                    </Modal.Body>
                </Modal>
            )}
        </div>
    );
}
