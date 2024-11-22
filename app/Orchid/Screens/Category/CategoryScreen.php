<?php

namespace App\Orchid\Screens\Category;

use App\Models\Category;
use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Switcher;
use Orchid\Support\Facades\Alert;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;

class CategoryScreen extends Screen
{

    public $name = 'Categories';
    public $description = 'Manage property categories';
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'categories' => Category::paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Category Screen';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Create Category')
                ->icon('plus')
                ->route('platform.category.create'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::table('categories', [

                TD::make('name', 'Categories')->sort()
                    ->cantHide()
                ->filter(Input::make()),

                TD::make('slug', 'Slug')->sort(),



                TD::make('actions', 'Actions')
                    ->align(TD::ALIGN_CENTER)
                    ->render(function (Category $category) {
                        return
                            Link::make('Edit')
                            ->icon('pencil')
                            ->route('platform.category.edit', $category->id)
                            ->toHtml() . // Convert to HTML

                            Button::make('Delete')
                            ->icon('trash')
                            ->method('delete')
                            ->confirm('Are you sure you want to delete this category?')
                            ->parameters([
                                'category' => $category->id,
                            ])
                            ->toHtml(); // Convert to HTML
                    }),

            ]),

        ];
    }

    public function delete(Category $category)
    {
        $category->delete();
        Alert::info('Category deleted successfully.');
    }
}
