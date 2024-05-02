import React, { useState, useEffect } from 'react';
import {
    DndContext,
    closestCenter,
    KeyboardSensor,
    PointerSensor,
    useSensor,
    useSensors,
  } from '@dnd-kit/core';
  import {
    SortableContext,
    sortableKeyboardCoordinates,
    rectSortingStrategy
  } from '@dnd-kit/sortable';

import SortablePostItem from './SortablePostItem';
import { isEqual } from 'lodash';
import { Grid } from './Grid';

export default function PostMediaDragDrop({ media: initialMedia = [], onMediaUpdate }) {
    const [media, setMedia] = useState(initialMedia);
    const sensors = useSensors(
        useSensor(PointerSensor),
        useSensor(KeyboardSensor, {
          coordinateGetter: sortableKeyboardCoordinates,
        })
      );
    useEffect(() => {
        if (!isEqual(initialMedia, media)) {
            setMedia(initialMedia);
        }
    }, [initialMedia]);

    return (
        <>
            {(media !== null && media.length > 0) ? (
                <DndContext
                    sensors={sensors}
                    collisionDetection={closestCenter}
                    onDragEnd={handleDragEnd}
                >
                    <SortableContext
                        items={media}
                        strategy={rectSortingStrategy}
                    >
                        <Grid columns={4}>
                            {media.map((mediaItem) => (
                                <SortablePostItem key={mediaItem.id} media={mediaItem} />
                            ))}
                        </Grid>
                    </SortableContext>
                </DndContext>
            ) : (
                <div>No images</div>
            )}
        </>
    );


    function handleDragEnd(event) {
        const {active, over} = event;
        console.log(typeof active.id, typeof over.id)
        console.log(active, over);
        if (active.id !== over.id) {
            const oldIndex = media.findIndex(item => item.id.toString() === active.id);
            const newIndex = media.findIndex(item => item.id.toString() === over.id);
            const newMediaItems = Array.from(media);
            newMediaItems.splice(newIndex, 0, newMediaItems.splice(oldIndex, 1)[0]);
            onMediaUpdate(newMediaItems);
          }
    }

};
