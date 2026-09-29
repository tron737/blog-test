{extends file="layouts/main.tpl"}

{block name="content"}

    <header class="category-header">
        <h1>{$category.name|escape}</h1>

        {if $category.description}
            <p>
                {$category.description|escape}
            </p>
        {/if}
    </header>

    <div class="sorting">
        <span>Сортировка:</span>

        <a
                href="/category/{$category.slug}?sort=date"
                class="{if $sort === 'date'}active{/if}"
        >
            По дате
        </a>

        <a
                href="/category/{$category.slug}?sort=views"
                class="{if $sort === 'views'}active{/if}"
        >
            По просмотрам
        </a>
    </div>

    <div class="post-grid">
        {foreach $posts as $post}
            {include
            file="partials/post-card.tpl"
            post=$post
            }
            {foreachelse}
            <p>В этой категории пока нет статей.</p>
        {/foreach}
    </div>

    {if $totalPages > 1}
        <nav class="pagination">
            {for $page=1 to $totalPages}
                <a
                        href="/category/{$category.slug}?sort={$sort}&page={$page}"
                        class="{if $page === $currentPage}active{/if}"
                >
                    {$page}
                </a>
            {/for}
        </nav>
    {/if}

{/block}