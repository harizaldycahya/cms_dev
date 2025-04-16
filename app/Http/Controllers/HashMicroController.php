<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HashMicro;

class HashMicroController extends Controller
{
    public function index()
    {
        $list_of_inputs = HashMicro::all(); // Retrieve all records using Eloquent

        return view('hashmicro.index')->with('list_of_inputs', $list_of_inputs);
    }

    public function show($id)
    {
        $input = HashMicro::findOrFail($id); // Find a record by ID or throw a 404 error

        return view('hashmicro.show')->with('input', $input);
    }

    public function store(Request $request)
    {
        // Retrieve inputs from the request
        $first_input = $request->input('first_input');
        $second_input = $request->input('second_input');

        // Calculate similarity percentage
        $first_input_chars = str_split($first_input);
        $unique_chars = array_unique($first_input_chars);
        $match_count = 0;
        $same_chars = [];

        foreach ($unique_chars as $char) {
            if (stripos($second_input, $char) !== false) {
                $match_count++;
                $same_chars[] = $char;
            }
        }

        $total_chars = count($first_input_chars);
        $similarity = $total_chars > 0 ? ($match_count / $total_chars) * 100 : 0;
        $same_chars_str = implode(', ', $same_chars);

        // Create a new record using Eloquent
        HashMicro::create([
            'first_input' => $first_input,
            'second_input' => $second_input,
            'similar_per_total' => $match_count . '/' . $total_chars,
            'same_char' => $same_chars_str,
            'similarity' => round($similarity, 2),
        ]);

        return redirect()->route('hashmicro.index')->with('success', 'Data successfully inserted!');
    }

    public function edit($id)
    {
        $input = HashMicro::findOrFail($id);

        return view('hashmicro.edit')->with('input', $input);
    }

    public function update(Request $request)
    {
        $id = $request->input('id');
        $first_input = $request->input('first_input');
        $second_input = $request->input('second_input');

        // Calculate similarity percentage
        $first_input_chars = str_split($first_input);
        $unique_chars = array_unique($first_input_chars);
        $match_count = 0;
        $same_chars = [];

        foreach ($unique_chars as $char) {
            if (stripos($second_input, $char) !== false) {
                $match_count++;
                $same_chars[] = $char;
            }
        }

        $total_chars = count($first_input_chars);
        $similarity = $total_chars > 0 ? ($match_count / $total_chars) * 100 : 0;
        $same_chars_str = implode(', ', $same_chars);

        // Find the record by ID and update it
        $hashmicro = HashMicro::findOrFail($id);
        $hashmicro->update([
            'first_input' => $first_input,
            'second_input' => $second_input,
            'similar_per_total' => $match_count . '/' . $total_chars,
            'same_char' => $same_chars_str,
            'similarity' => round($similarity, 2),
        ]);

        return redirect()->route('hashmicro.show', $id)->with('success', 'Data successfully updated!');
    }

    public function destroy($id)
    {
        // Find the record by ID and delete it
        $hashmicro = HashMicro::findOrFail($id);
        $hashmicro->delete();

        return redirect()->route('hashmicro.index')->with('success', 'Data successfully deleted!');
    }
}