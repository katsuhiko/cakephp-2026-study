-- see https://book.cakephp.org/5/ja/quickstart.html#cms

INSERT INTO users (email, password, created, modified)
VALUES
('cakephp@example.com', 'secret', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO articles (user_id, title, slug, body, published, created, modified)
VALUES
(1, 'First Post', 'first-post', 'This is the first post.', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
