<?php

namespace App\Orchid\Screens\Category;

use App\Models\Category;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Support\Facades\Alert;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Select;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CategoryEditScreen extends Screen
{
    public $name = 'Create/Edit Category';
    public $description = 'Create or edit a category';
    public $category;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Category $category): iterable
    {
        return [
            'category' => $category,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'CategoryEditScreen';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [

            Button::make('Delete')
                ->icon('trash')
                ->method('remove')
                ->canSee($this->category->exists),

            Button::make('Save')
            ->icon('check')
            ->method('save')
            ->class('btn btn-success'),

        ];
    }
    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        return [
            Layout::tabs([
                'General' => Layout::rows([
                    Input::make('category.name')
                        ->title('Name')
                         ->value($this->category->name) // Prefill the name field with category data

                        ->placeholder('Enter category name')
                        ->required(),

                    TextArea::make('category.description')
                        ->title('Description')
                        ->rows(3)
                        ->placeholder('Enter category description'),

                    Select::make('category.parent_category_id')
                        ->fromModel(Category::class, 'name', 'id')
                        ->empty('No Parent Category')
                        ->title('Parent Category'),
                ]),

                'SEO' => Layout::rows([
                    Input::make('category.meta_title')
                        ->title('Meta Title')
                        ->placeholder('Enter meta title'),

                    TextArea::make('category.meta_description')
                        ->title('Meta Description')
                        ->rows(3)
                        ->placeholder('Enter meta description'),

                    Input::make('category.slug')
                        ->title('Slug')
                        ->placeholder('Enter unique slug')
                        ->help('The slug is used to generate a URL-friendly name for the category')
                        ->required(),
                ]),
            ]),
        ];
    }


    /**
     * Save the category.
     *
     * @param Category $category
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save(Category $category, Request $request)
    {

        DB::beginTransaction();

        try {
            // Define validation rules
            $rules = [
                'category.name' => 'required|string|max:255',
                'category.slug' => 'required|string|max:255|unique:categories,slug,' . ($category->id ?? 'NULL'),
                'category.description' => 'nullable|string',
            ];

            // Define custom error messages
            $messages = [
                'category.slug.unique' => 'The slug has already been taken.',
                'category.name.required' => 'The category name is required.',
                'category.slug.required' => 'The slug is required.'
            ];

            // Create a Validator instance
            $validator = Validator::make($request->all(), $rules, $messages);

            // Check if validation fails
            if ($validator->fails()) {
                // Retrieve all error messages
                $errors = $validator->errors()->all();
                Log::error('Validation errors: ' . implode(', ', $errors));
                // Combine error messages into a single string
                $combinedMessage = implode(' | ', $errors);
                throw new \Exception("Validation errors: " . $combinedMessage);
            }

            // Proceed with handling the data if validation passes
            $data = $request->get('category');

            $category->fill($data);
            $category->save();
            // Get the last inserted ID
            $lastInsertId = $category->id;

            DB::commit();
            // Flash success message
            Alert::info('Category saved successfully.');

            return redirect()->route('platform.categories');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Failed Due To :' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }



    /**
     * Remove the category.
     *
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(Category $category)
    {
        $category->delete();
        Alert::info('Category deleted successfully.');
        return redirect()->route('platform.categories');
    }
}
