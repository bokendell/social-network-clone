export default function Post (post) {
    if (!post) {
        return <p>loading</p>;
    }
    console.log(post);
    return (
        <div className="border p-4 rounded-md">
            <h3 className="text-lg font-semibold">{post.user.name}</h3>
            {post.images ? (
                <div className="space-x-4">
                    {post.images.map(image => (
                        <img key={image.id} src={image.url} alt={image.alt} className="w-32 h-32 object-cover rounded" />
                    ))}
                </div>
            ) : null}
            {post.videos ? (
                <div className="space-x-4">
                    {post.videos.map(video => (
                        <video key={video.id} src={video.url} controls className="w-32 h-32 object-cover rounded" />
                    ))}
                </div>
            ) : null}
            <p className="text-gray-700">{post.content}</p>
            <p>{post.likes.count()} likes</p>
            <p>{post.comments.count()} comments</p>
            {post.comments.map(comment => (
                <div key={comment.id} className="border p-4 rounded-md">
                    <h3 className="text-lg font-semibold">{comment.user.name}</h3>
                    <p className="text-gray-700">{comment.content}</p>
                </div>
            ))}
            <p>{post.reposts.count} reposts</p>
        </div>
    );
}
