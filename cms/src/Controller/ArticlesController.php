<?php
declare(strict_types=1);

namespace App\Controller;

use CmsCore\Application\UseCase\CreateArticleUseCase;

/**
 * Articles Controller
 *
 * @property \App\Model\Table\ArticlesTable $Articles
 */
class ArticlesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Articles->find()
            ->contain(['Users']);
        $articles = $this->paginate($query);

        $this->set(compact('articles'));
    }

    /**
     * View method
     *
     * @param string $id Article id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(string $id)
    {
        $article = $this->Articles->get($id, contain: ['Users', 'Tags']);
        $this->set(compact('article'));
    }

    /**
     * Add method
     *
     * @param \CmsCore\Application\UseCase\CreateArticleUseCase $createArticleUseCase Create article use case
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add(CreateArticleUseCase $createArticleUseCase)
    {
        $article = $this->Articles->newEmptyEntity();

        if ($this->request->is('post')) {
            // Adapter層での入力形式バリデーション (CakePHPのバリデーション)
            $article = $this->Articles->patchEntity($article, (array)$this->request->getData());

            /**
             * @var array{
             *     user_id: string,
             *     title: string,
             *     slug: string,
             *     body: string,
             *     published: string,
             *     tag_ids: array<string>
             * } $data
             */
            $data = (array)$this->request->getData();

            if (!$article->hasErrors()) {
                // UseCaseを実行
                [
                    'success' => $success,
                    'articleId' => $articleId,
                    'errors' => $errors,
                ] = $createArticleUseCase->execute([
                    'user_id' => (int)($data['user_id'] ?? 0),
                    'title' => (string)($data['title'] ?? ''),
                    'slug' => (string)($data['slug'] ?? ''),
                    'body' => (string)($data['body'] ?? ''),
                    'published' => (bool)($data['published'] ?? false),
                    'tag_ids' => array_map(fn(mixed $id): int => (int)$id, $data['tag_ids'] ?? []),
                ]);

                if ($success) {
                    $this->Flash->success(__('The article has been saved.'));

                    return $this->redirect(['action' => 'index']);
                }

                // Domain層のエラーを表示
                foreach ($errors as $error) {
                    $this->Flash->error($error);
                }
            } else {
                $this->Flash->error(__('The article could not be saved. Please, try again.'));
            }
        }

        $users = $this->Articles->Users->find('list', limit: 200)->all();
        $tags = $this->Articles->Tags->find('list', limit: 200)->all();
        $this->set(compact('article', 'users', 'tags'));
    }

    /**
     * Edit method
     *
     * @param string $id Article id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(string $id)
    {
        $article = $this->Articles->get($id, contain: ['Tags']);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $article = $this->Articles->patchEntity($article, (array)$this->request->getData());
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('The article has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The article could not be saved. Please, try again.'));
        }
        $users = $this->Articles->Users->find('list', limit: 200)->all();
        $tags = $this->Articles->Tags->find('list', limit: 200)->all();
        $this->set(compact('article', 'users', 'tags'));
    }

    /**
     * Delete method
     *
     * @param string $id Article id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(string $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $article = $this->Articles->get($id);
        if ($this->Articles->delete($article)) {
            $this->Flash->success(__('The article has been deleted.'));
        } else {
            $this->Flash->error(__('The article could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
