<article class="post-card">
    {if $post.image}
        <a href="/post/{$post.slug}">
            <img
                    src="{$post.image}"
                    alt="{$post.title|escape}"
                    class="post-card__image"
            >
        </a>
    {/if}

    <div class="post-card__content">
        <h3 class="post-card__title">
            <a href="/post/{$post.slug}">
                {$post.title|escape}
            </a>
        </h3>

        <p class="post-card__description">
            {$post.description|escape}
        </p>

        <div class="post-card__meta">
            <span>
                {$post.published_at}
            </span>

            <span>
                Просмотров: {$post.views}
            </span>
        </div>
    </div>
</article>