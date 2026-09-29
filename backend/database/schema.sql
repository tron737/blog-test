CREATE TABLE categories
(
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    slug        VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE posts
(
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    image        VARCHAR(255) NULL,
    title        VARCHAR(255) NOT NULL,
    slug         VARCHAR(255) NOT NULL UNIQUE,
    description  TEXT         NOT NULL,
    content      LONGTEXT     NOT NULL,
    views        INT UNSIGNED NOT NULL DEFAULT 0,
    published_at DATETIME     NOT NULL,
    created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX        idx_posts_published_at (published_at),
    INDEX        idx_posts_views (views)
);

CREATE TABLE post_categories
(
    post_id     INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,

    PRIMARY KEY (post_id, category_id),

    CONSTRAINT fk_post_categories_post
        FOREIGN KEY (post_id)
            REFERENCES posts (id)
            ON DELETE CASCADE,

    CONSTRAINT fk_post_categories_category
        FOREIGN KEY (category_id)
            REFERENCES categories (id)
            ON DELETE CASCADE
);