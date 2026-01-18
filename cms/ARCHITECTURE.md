# プロジェクト設計・開発方針

## アーキテクチャ概要
- **コアフレームワーク**: CakePHP 5
- **パターン**: ヘキサゴナルアーキテクチャ
- **分離方針**: 業務ロジックを `packages/CmsCore` に集約し、フレームワークから独立させる。

## レイヤー構造と責務
依存方向：[Adapter] → [Application] → [Domain], [Infrastructure] → [Domain]

- **Domain**: 純粋な業務ロジック。Entity, ValueObject, Interface。他層に依存しない。
- **Application**: 業務手順の制御（UseCase）。Domain のみを利用。
- **Infrastructure**: 技術的詳細（CakeORM 等の実装）。Interface を具体化する。
- **Adapter**: 外部接続（Controller, Console）。UseCase を呼び出し入出力を変換。

## ディレクトリ構造
- **packages/CmsCore/** (ドメイン・ロジックコア)
    - **Domain/**: Entity, ValueObject, RepositoryInterface (業務ルールとインターフェース)
    - **Application/**: UseCase(更新系), QueryInterface(参照系Port) (アプリケーション手順)
    - **Infrastructure/**: Persistence (CakeORMによる具体的な実装)
    - **Test/**
        - **Domain/**: Domain層の単体テスト (純粋なPHP)
        - **Application/**: UseCaseのテスト (純粋なPHP、Repositoryをモック化)
- **src/**: CakePHP Adapter (Controller, Table定義, DI設定)
- **tests/TestCase/**: Integration テスト (実DB・HTTPリクエストを伴う結合テスト)

## 命名規則
- **UseCase**: `[動詞][対象]UseCase` (例: `UpdateArticleUseCase`)
- **Repository Port**: `[対象]RepositoryInterface`
- **Repository Adapter**: `Cake[対象]Repository`
- **Query Port**: `[対象]QueryServiceInterface`

## 設計ポリシー
- **ドメインモデル**: `readonly` プロパティによる不変性(Immutability)を保持。業務ルールは ValueObject に封じ込める。
- **データ受け渡し**: UseCase は `array` で入力を受け取り、内部でドメインモデルへ変換する。
- **認証**: ステートレスを維持。Controller から UseCase へ `UserId` (ValueObject) を引数で渡す。
- **バリデーション**:
    - **Adapter層 (Cake)**: 入力形式、必須チェックを担当。
    - **Domain層 (Core)**: 業務ルールチェックを担当。違反時は例外をスロー。
- **排他制御**: `Articles` 等の主要テーブルは、`version` プロパティを用いた楽観的ロックを Entity レベルで管理。

## テスト戦略
1. **Unit Test**: Domain層のロジック検証。モック不要。高速実行。 (`packages/CmsCore/Test/Domain`)
2. **Application Test**: UseCaseのフロー検証。Repositoryのみモック化。 (`packages/CmsCore/Test/Application`)
3. **Integration Test**: ControllerからDBまで。実DBを使用。 (`tests/TestCase`)

## コーディングルール
### 基本原則
- **PSR-12準拠**: 基本的な命名、スタイルは PSR-12 に従う。
- **型定義の徹底**: 引数、戻り値には必ず型を定義する。
- **厳格モード**: 全ファイル冒頭に `declare(strict_types=1);` を記述する。
- **DI (依存性注入)**: コンストラクタインジェクションを徹底。クラス内での `new` (Service/Repository) は禁止。

### プロパティ・変数の命名規則
基本は PSR-12 に準拠するが、CakePHP の Table オブジェクトのみ例外とする。

- **基本ルール (PSR-12)**
    - 対象: Domain層、Application層、および Table 以外の全変数・プロパティ。
    - 規則: **キャメルケース（小文字開始）** を使用する（例: `$this->articleRepository`, `$articleId`）。
- **Tableクラスの特例 (CakePHP規約)**
    - 対象: `src/Model/Table` 以下のクラス、およびそれらを保持する変数・プロパティ。
    - 規則: **アッパーキャメルケース（大文字開始・複数形）** を使用する（例: `$this->Articles`, `$Articles->find()`）。
    - 意図: CakePHPの「設定より規約(CoC)」を維持し、強力な権限を持つ Table オブジェクトであることを視覚的に強調するため。

### ドメイン設計の詳細
- **Entity生成**: `static create(array $data): self` という名称の Named Constructor を実装する。
- **副作用**: 状態が変化する場合は、常に新しいインスタンスを生成して返す。

### エラーハンドリングとログ記録
- **例外の層別責務**:
    - **Domain**: 業務ルール違反
    - **Application**: 処理失敗
    - **Adapter**: 入出力・通信エラー
- **例外の伝播**: Domain から Adapter へ一方向に伝播させ、Controller 等の最外殻で HTTP ステータスやレスポンス形式へ変換する。
- **ログ記録の原則**: Application層 (UseCase) で実施する。
    - **理由**: 「誰が・何の処理で」といったビジネスコンテキストを最も把握している層で記録し、Adapter層での二重記録を防止するため。

## データベース管理
- **マイグレーション**: CakePHP Migrations を使用
- **命名**: `YYYYMMDDHHMMSS_[動詞][対象].php` (例: `20260118_CreateArticles.php`)
- **Rollback**: 本番環境での Rollback は原則禁止、Forward-only で修正
