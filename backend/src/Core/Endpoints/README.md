# Endpoints

Здесь хранятся EndPoints (могут называться по-разному - Controllers, Actions, Commands).

Их основная задача получить данные из ввода, проверять их и передать их в UseCase.

Обычно этот слой обильно использует функционал фреймворка, т.к. получает от этого
максимальное преимущество, а абстракция от фреймворка здесь почти ничего не дает, т.к. слой максимально тонкий.

Typical controller looks like
```php
class Controller
    public function __invoke(Request $request): FoundResponse
    {
        $arg1 = $request->getParam1();
        $arg2 = $request->getParam2();
        try {
            $result = $this->usecase->execute($arg1, $arg2);
        } catch (NothingFound $e) {
            throw new NotFoundHttpException('nothing', $e);
        } catch (AlreadyProceed $e) {
            throw new ConflictHttpException('conflict', $e);
        } catch (BadStatus $e) {
            throw new UnprocessableEntity('conflict', $e);
        }
        
        return new FoundResponse($result);
    }
}
```

To implement CLI command override `execute()` instead using `setCode()`.
Typical command look like:
```php
final class GenerateFromOpenApi extends Command
{
    protected static $defaultName = 'some-namespace:some-action';
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Generating entrypoints from ' . $this->specificationPath);
        try {
            $this->specification->generateCode($this->specificationPath);

            return self::SUCCESS;
        } catch (SpecificationException $e) {
            $output->writeln('Error reading specification: ' . $e->getMessage());
        }

        return self::FAILURE;
    }
}
```
