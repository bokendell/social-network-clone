import { Avatar } from '@/Components/CatalystComponents/avatar'
import { Link } from '@inertiajs/react';
import { Text, Strong } from '@/Components/CatalystComponents/text';
import { formatDistanceToNow, parseISO } from 'date-fns';

export default function PostHeader({ post, disabled = false}) {
    // console.log('PostHeader', post);
    const formatDateTime = (dateTime) => {
        const dateTimeString = '2023-12-31T23:59:59Z';
        const dateTimeObject = parseISO(dateTimeString);
        return formatDistanceToNow(dateTimeObject, new Date());
    }

    return (
            <div className="flex items-center justify-between mb-2">
                {disabled ?
                    <div className="flex items-center">
                        <Avatar className='mr-3 size-10' initials={post.user.name.charAt(0)} src={post.user.profile_pic_url} />
                        <div><strong>{post.user.username}</strong></div>
                    </div>
                    :
                    <Link href={`/profile/${post.user.id}`}className="flex items-center">
                        <Avatar className='mr-3 size-10' initials={post.user.name.charAt(0)} src={post.user.profile_pic_url} />
                        <Strong>{post.user.username}</Strong>
                    </Link>
                }
                <Text className="ml-auto text-sm">{formatDateTime(post.updated_at)} ago</Text>
            </div>
    );
}
