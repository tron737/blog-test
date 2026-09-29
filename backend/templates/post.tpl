{extends file="layouts/main.tpl"}

{block name="content"}

    <article class="post">

        <h1>{$post.title|escape}</h1>

        <div class="post__meta">
            <span>{$post.published_at}</span>
            <span>Просмотров: {$post.views}</span>
        </div>

        {if $post.image}
            <img
                    src="{$post.image}"
                    alt="{$post.title|escape}"
                    class="post__image"
            >
        {/if}

        <p class="post__description">
            {$post.description|escape}
        </p>

        <div class="post__content">
            {$post.content|escape|nl2br}
        </div>

    </article>

    {if $similarPosts}
        <section class="similar-posts">
            <h2>Похожие статьи</h2>

            <div class="post-grid">
                {foreach $similarPosts as $post}
                    {include
                    file="partials/post-card.tpl"
                    post=$post
                    }
                {/foreach}
            </div>
        </section>
    {/if}

{/block}