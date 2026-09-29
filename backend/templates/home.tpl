{extends file="layouts/main.tpl"}

{block name="content"}

    <h1>Блог</h1>

    {foreach $categories as $category}
        <section class="category-section">

            <div class="category-section__header">
                <div>
                    <h2>
                        {$category.name|escape}
                    </h2>

                    {if $category.description}
                        <p>
                            {$category.description|escape}
                        </p>
                    {/if}
                </div>

                <a href="/category/{$category.slug}">
                    Все статьи
                </a>
            </div>

            <div class="post-grid">
                {foreach $category.posts as $post}
                    {include
                    file="partials/post-card.tpl"
                    post=$post
                    }
                {/foreach}
            </div>

        </section>
    {/foreach}

{/block}