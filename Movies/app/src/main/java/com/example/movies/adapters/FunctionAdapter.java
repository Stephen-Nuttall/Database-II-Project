package com.example.movies.adapters;

import android.content.Context;
import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.example.movies.R;
import com.example.movies.activities.ActivityClubJoinLeave;
import com.example.movies.activities.ActivityCreateClub;
import com.example.movies.item1;
import com.example.movies.item2;
import com.example.movies.item3;
import com.example.movies.item4;

import java.util.List;

public class FunctionAdapter extends RecyclerView.Adapter<FunctionAdapter.FunctionViewHolder> {

    private final List<String> titles;
    private final List<String> subtitles;
    private final Context context;

    public FunctionAdapter(Context context, List<String> titles, List<String> subtitles) {
        this.context = context;
        this.titles = titles;
        this.subtitles = subtitles;
    }

    @NonNull
    @Override
    public FunctionViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.item_function, parent, false);
        return new FunctionViewHolder(view);
    }


    @Override
    public void onBindViewHolder(@NonNull FunctionViewHolder holder, int position) {
        holder.title.setText(titles.get(position));
        holder.subtitle.setText(subtitles.get(position));

        if (subtitles != null && position < subtitles.size()) {
            holder.subtitle.setVisibility(View.VISIBLE);
            holder.subtitle.setText(subtitles.get(position));
        } else {
            holder.subtitle.setVisibility(View.GONE);
        }

        holder.itemView.setOnClickListener(v -> {
            switch (position) {
                case 0: context.startActivity(new Intent(context, item1.class)); break;
                case 1: context.startActivity(new Intent(context, item2.class)); break;
                case 2: context.startActivity(new Intent(context, item3.class)); break;
                case 3: context.startActivity(new Intent(context, item4.class)); break;
                case 4: context.startActivity(new Intent(context, ActivityCreateClub.class)); break;
                case 5: context.startActivity(new Intent(context, ActivityClubJoinLeave.class)); break;
            }
        });
    }

    @Override
    public int getItemCount() {
        return titles.size();
    }

    public static class FunctionViewHolder extends RecyclerView.ViewHolder {
        TextView title, subtitle;
        public FunctionViewHolder(@NonNull View itemView) {
            super(itemView);
            title = itemView.findViewById(R.id.titleText);
            subtitle = itemView.findViewById(R.id.subText);
        }
    }
}
