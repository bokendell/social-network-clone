import { useQuery } from '@tanstack/react-query';
import axios from 'axios';
import PostHeader from '@/Components/PostHeader';
import PostMedia from '@/Components/PostMedia';
import PostInteractions from '@/Components/PostInteractions';



export default function Post ({userID}) {
    const { data, isLoading, error } =
    useQuery({
        queryKey: ['posts'],
        queryFn: async () => {
            if (userID) {
                const response = await axios.get(route('feed.posts.user', userID));
                return response.data;
            }
            else {
                const response = await axios.get(route('feed.posts'));
                return response.data;
            }
      },
    });


    return (
        <div>
            {isLoading && (
                <div>Loading...</div>
            )}
            {error && (
                <div>Error loading posts : {error}</div>
            )}
            {data && data.posts.map(post => (
                <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-5 p-5" key={post.id}>
                    <PostHeader post={post}/>
                    <PostMedia post={post} />
                    <PostInteractions post={post} />
                </div>
            ))}
        </div>

    )
}
