package com.example.movies.activities;

import android.os.Bundle;

import androidx.activity.EdgeToEdge;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.example.movies.R;
import com.example.movies.adapters.FunctionAdapter;

import java.util.Arrays;
import java.util.Collections;
import java.util.List;

public class MainActivity extends AppCompatActivity {

    RecyclerView recyclerView;
    List<String> subtitles = Arrays.asList(
            "Create an Account",
            "Browse and Register Courses",
            "View Taken and Current Courses",
            "Instructor Records",
            "Create a Club",
            "Join/Leave a Club"
    );

    List<String> titles = Arrays.asList(
            "",
            "",
            "",
            "",
            "",
            ""
    );

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        recyclerView = findViewById(R.id.recyclerView);
        recyclerView.setLayoutManager(new LinearLayoutManager(this));
        recyclerView.setAdapter(new FunctionAdapter(this, titles, subtitles));
    }
}
