import PostHeader from '@/Components/Posts/PostHeader';
import PostMedia from '@/Components/Posts/PostMedia';
import PostInteractions from '@/Components/Posts/PostInteractions';



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
