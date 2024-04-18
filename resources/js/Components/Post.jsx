import PostHeader from '@/Components/PostHeader';
import PostMedia from '@/Components/PostMedia';
import PostInteractions from '@/Components/PostInteractions';



export default function Post ({posts, auth}) {
    return (
        <div>
            {posts.map(post => (
                <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-5 p-5" key={post.id}>
                    <PostHeader post={post}/>
                    <PostMedia post={post} />
                    <PostInteractions post={post} auth={auth}/>
                </div>
            ))}
        </div>

    )
}
